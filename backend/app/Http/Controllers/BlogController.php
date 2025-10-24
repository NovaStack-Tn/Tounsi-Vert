<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogComment;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BlogController extends Controller
{
    /**
     * Display a listing of blogs with filters and sorting.
     */
    public function index(Request $request)
    {
        $query = Blog::with(['user'])->published();

        // Search filter
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Sort filter
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'popular':
                $query->popular();
                break;
            case 'most_liked':
                $query->orderBy('likes_count', 'desc');
                break;
            case 'most_viewed':
                $query->orderBy('views_count', 'desc');
                break;
            case 'most_commented':
                $query->orderBy('comments_count', 'desc');
                break;
            default:
                $query->latest();
        }

        $blogs = $query->paginate(10);

        // Get top organizations for sidebar ads (based on score)
        $topOrganizations = Organization::where('is_verified', true)
            ->whereHas('owner', function($q) {
                $q->orderBy('score', 'desc');
            })
            ->with('owner')
            ->limit(5)
            ->get()
            ->sortByDesc('owner.score')
            ->take(5);

        return view('blogs.index', compact('blogs', 'topOrganizations'));
    }

    /**
     * Show the form for creating a new blog.
     */
    public function create()
    {
        return view('blogs.create');
    }

    /**
     * Store a newly created blog in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
            'video' => 'nullable|mimes:mp4,mov,avi,wmv|max:51200',
            'ai_assisted' => 'nullable|boolean',
        ]);

        $imagesPaths = [];
        $videoPath = null;
        $hasImages = false;
        $hasVideo = false;

        // Handle multiple images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagesPaths[] = $image->store('blogs/images', 'public');
            }
            $hasImages = true;
        }

        // Handle video
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('blogs/videos', 'public');
            $hasVideo = true;
        }

        $blog = Blog::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'content' => $request->content,
            'image_path' => !empty($imagesPaths) ? $imagesPaths[0] : null, // Keep first image for backward compatibility
            'images_paths' => $imagesPaths,
            'video_path' => $videoPath,
            'has_images' => $hasImages,
            'has_video' => $hasVideo,
            'ai_assisted' => $request->boolean('ai_assisted', false),
            'is_published' => true,
        ]);

        // Check if from organizer panel
        if ($request->routeIs('organizer.*') || str_contains(url()->previous(), '/organizer/')) {
            return redirect()->route('organizer.blogs.show', $blog)
                ->with('success', 'Blog created successfully!');
        }

        return redirect()->route('blogs.show', $blog)
            ->with('success', 'Blog created successfully!');
    }

    /**
     * Display the specified blog.
     */
    public function show(Blog $blog)
    {
        // Increment view count
        $blog->incrementViews();

        // Load relationships
        $blog->load(['user', 'comments.user', 'comments.replies.user']);

        // Get related blogs
        $relatedBlogs = Blog::published()
            ->where('id', '!=', $blog->id)
            ->where('user_id', $blog->user_id)
            ->latest()
            ->take(3)
            ->get();

        return view('blogs.show', compact('blog', 'relatedBlogs'));
    }

    /**
     * Show the form for editing the specified blog.
     */
    public function edit(Blog $blog)
    {
        $this->authorize('update', $blog);

        return view('blogs.edit', compact('blog'));
    }

    /**
     * Update the specified blog in storage.
     */
    public function update(Request $request, Blog $blog)
    {
        $this->authorize('update', $blog);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
            'video' => 'nullable|mimes:mp4,mov,avi,wmv|max:51200',
            'remove_images' => 'nullable|array',
            'remove_video' => 'nullable|boolean',
        ]);

        $updateData = [
            'title' => $request->title,
            'content' => $request->content,
        ];

        // Handle image removal
        if ($request->has('remove_images')) {
            $existingImages = $blog->images_paths ?? [];
            foreach ($request->remove_images as $imageToRemove) {
                if (($key = array_search($imageToRemove, $existingImages)) !== false) {
                    Storage::disk('public')->delete($imageToRemove);
                    unset($existingImages[$key]);
                }
            }
            $updateData['images_paths'] = array_values($existingImages);
            $updateData['has_images'] = count($existingImages) > 0;
        }

        // Handle new images
        if ($request->hasFile('images')) {
            $existingImages = $updateData['images_paths'] ?? $blog->images_paths ?? [];
            foreach ($request->file('images') as $image) {
                $existingImages[] = $image->store('blogs/images', 'public');
            }
            $updateData['images_paths'] = $existingImages;
            $updateData['image_path'] = $existingImages[0] ?? null;
            $updateData['has_images'] = true;
        }

        // Handle video removal
        if ($request->boolean('remove_video') && $blog->video_path) {
            Storage::disk('public')->delete($blog->video_path);
            $updateData['video_path'] = null;
            $updateData['has_video'] = false;
        }

        // Handle new video
        if ($request->hasFile('video')) {
            if ($blog->video_path) {
                Storage::disk('public')->delete($blog->video_path);
            }
            $updateData['video_path'] = $request->file('video')->store('blogs/videos', 'public');
            $updateData['has_video'] = true;
        }

        $blog->update($updateData);

        // Check if from organizer panel
        if ($request->routeIs('organizer.*') || str_contains(url()->previous(), '/organizer/')) {
            return redirect()->route('organizer.blogs.show', $blog)
                ->with('success', 'Blog updated successfully!');
        }

        return redirect()->route('blogs.show', $blog)
            ->with('success', 'Blog updated successfully!');
    }

    /**
     * Remove the specified blog from storage.
     */
    public function destroy(Blog $blog)
    {
        $this->authorize('delete', $blog);

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

        $blog->delete();

        // Check if from organizer panel
        if (str_contains(url()->previous(), '/organizer/')) {
            return redirect()->route('organizer.blogs.index')
                ->with('success', 'Blog deleted successfully!');
        }

        return redirect()->route('blogs.index')
            ->with('success', 'Blog deleted successfully!');
    }

    /**
     * Toggle like on a blog.
     */
    public function toggleLike(Blog $blog)
    {
        $user = auth()->user();

        if ($blog->isLikedBy($user)) {
            // Unlike
            $blog->likedBy()->detach($user->id);
            $blog->decrement('likes_count');
            $liked = false;
        } else {
            // Like
            $blog->likedBy()->attach($user->id);
            $blog->increment('likes_count');
            $liked = true;
        }

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'liked' => $liked,
                'likes_count' => $blog->likes_count,
            ]);
        }

        return back()->with('success', $liked ? 'Blog liked!' : 'Blog unliked!');
    }

    /**
     * Add a comment to a blog.
     */
    public function addComment(Request $request, Blog $blog)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:blog_comments,id',
        ]);

        $comment = BlogComment::create([
            'blog_id' => $blog->id,
            'user_id' => auth()->id(),
            'comment' => $request->comment,
            'parent_id' => $request->parent_id,
        ]);

        $blog->increment('comments_count');

        if (request()->wantsJson()) {
            $comment->load('user');
            return response()->json([
                'success' => true,
                'comment' => $comment,
            ]);
        }

        return back()->with('success', 'Comment added successfully!');
    }

    /**
     * Delete a comment.
     */
    public function deleteComment(BlogComment $comment)
    {
        $this->authorize('delete', $comment);

        $blog = $comment->blog;
        $comment->delete();
        $blog->decrement('comments_count');

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Comment deleted successfully!');
    }

    /**
     * Get user's blogs for dashboard.
     */
    public function myBlogs()
    {
        $blogs = auth()->user()->blogs()
            ->withCount(['comments', 'likedBy'])
            ->latest()
            ->paginate(10);

        return view('blogs.my-blogs', compact('blogs'));
    }

    /**
     * Organizer: List all blogs by the authenticated user.
     */
    public function organizerIndex()
    {
        $blogs = auth()->user()->blogs()
            ->withCount(['comments', 'likedBy'])
            ->latest()
            ->paginate(12);

        return view('organizer.blogs.index', compact('blogs'));
    }

    /**
     * Organizer: Show create blog form.
     */
    public function organizerCreate()
    {
        return view('organizer.blogs.create');
    }

    /**
     * Organizer: Show single blog with full details.
     */
    public function organizerShow(Blog $blog)
    {
        // Authorize user can only view their own blogs in organizer panel
        if ($blog->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $blog->load(['user', 'comments.user', 'comments.replies.user', 'likedBy']);

        return view('organizer.blogs.show', compact('blog'));
    }

    /**
     * Organizer: Show edit blog form.
     */
    public function organizerEdit(Blog $blog)
    {
        $this->authorize('update', $blog);

        return view('organizer.blogs.edit', compact('blog'));
    }
}
