<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminBlogController extends Controller
{
    /**
     * Display a listing of all blogs with stats
     */
    public function index(Request $request)
    {
        $query = Blog::with(['user', 'comments', 'likes']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by media type
        if ($request->filled('media_type')) {
            switch ($request->media_type) {
                case 'images':
                    $query->where('has_images', true);
                    break;
                case 'video':
                    $query->where('has_video', true);
                    break;
                case 'none':
                    $query->where('has_images', false)->where('has_video', false);
                    break;
            }
        }

        // Filter by AI assisted
        if ($request->filled('ai_assisted')) {
            $query->where('ai_assisted', $request->ai_assisted === 'yes');
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sort
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'oldest':
                $query->oldest();
                break;
            case 'most_liked':
                $query->withCount('likes')->orderBy('likes_count', 'desc');
                break;
            case 'most_commented':
                $query->withCount('comments')->orderBy('comments_count', 'desc');
                break;
            case 'most_viewed':
                $query->orderBy('views_count', 'desc');
                break;
            default:
                $query->latest();
        }

        $blogs = $query->paginate(15)->withQueryString();

        // Statistics
        $stats = [
            'total_blogs' => Blog::count(),
            'total_comments' => BlogComment::count(),
            'total_likes' => \DB::table('blog_likes')->count(),
            'ai_assisted_blogs' => Blog::where('ai_assisted', true)->count(),
            'blogs_with_images' => Blog::where('has_images', true)->count(),
            'blogs_with_videos' => Blog::where('has_video', true)->count(),
            'total_views' => Blog::sum('views_count'),
        ];

        return view('admin.blogs.index', compact('blogs', 'stats'));
    }

    /**
     * Display the specified blog with full details
     */
    public function show(Blog $blog, Request $request)
    {
        $blog->load(['user', 'likes', 'comments.user']);

        // Search comments
        $commentsQuery = $blog->comments();

        if ($request->filled('comment_search')) {
            $search = $request->comment_search;
            $commentsQuery->where(function($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('id', $search);
                  });
            });
        }

        $comments = $commentsQuery->with('user')->latest()->paginate(10)->withQueryString();

        // Get creator organization if exists
        $organization = null;
        if ($blog->user && $blog->user->role === 'organizer') {
            $organization = $blog->user->organizations()->first();
        }

        return view('admin.blogs.show', compact('blog', 'comments', 'organization'));
    }

    /**
     * Delete a blog
     */
    public function destroy(Blog $blog)
    {
        try {
            // Delete all images
            if ($blog->images_paths) {
                foreach ($blog->images_paths as $imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
            }

            // Delete video
            if ($blog->video_path) {
                Storage::disk('public')->delete($blog->video_path);
            }

            // Delete old single image if exists
            if ($blog->image_path) {
                Storage::disk('public')->delete($blog->image_path);
            }

            $blog->delete();

            return response()->json([
                'success' => true,
                'message' => 'Blog deleted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete blog: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a comment
     */
    public function destroyComment(BlogComment $comment)
    {
        try {
            $comment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete comment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get blog statistics for dashboard
     */
    public function stats()
    {
        $stats = [
            'total_blogs' => Blog::count(),
            'blogs_today' => Blog::whereDate('created_at', today())->count(),
            'blogs_this_week' => Blog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'blogs_this_month' => Blog::whereMonth('created_at', now()->month)->count(),
            'total_comments' => BlogComment::count(),
            'total_likes' => \DB::table('blog_likes')->count(),
            'ai_assisted' => Blog::where('ai_assisted', true)->count(),
            'with_images' => Blog::where('has_images', true)->count(),
            'with_videos' => Blog::where('has_video', true)->count(),
            'total_views' => Blog::sum('views_count'),
            'avg_comments_per_blog' => round(BlogComment::count() / max(Blog::count(), 1), 2),
            'avg_likes_per_blog' => round(\DB::table('blog_likes')->count() / max(Blog::count(), 1), 2),
        ];

        return response()->json($stats);
    }
}
