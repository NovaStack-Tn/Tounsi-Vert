<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Blog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'image_path',
        'video_path',
        'media_type',
        'is_published',
        'ai_assisted',
        'views_count',
        'likes_count',
        'comments_count',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'ai_assisted' => 'boolean',
        'views_count' => 'integer',
        'likes_count' => 'integer',
        'comments_count' => 'integer',
    ];

    /**
     * Get the user that owns the blog.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all comments for the blog.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(BlogComment::class)->whereNull('parent_id')->latest();
    }

    /**
     * Get all comments (including replies) for the blog.
     */
    public function allComments(): HasMany
    {
        return $this->hasMany(BlogComment::class);
    }

    /**
     * Get users who liked the blog.
     */
    public function likedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'blog_likes')->withTimestamps();
    }

    /**
     * Check if a user has liked this blog.
     */
    public function isLikedBy(?User $user = null): bool
    {
        if (!$user) {
            $user = auth()->user();
        }

        if (!$user) {
            return false;
        }

        return $this->likedBy()->where('user_id', $user->id)->exists();
    }

    /**
     * Increment view count.
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Scope to get published blogs.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope to order by popularity (likes + comments + views).
     */
    public function scopePopular($query)
    {
        return $query->orderByRaw('(likes_count * 3 + comments_count * 2 + views_count) DESC');
    }

    /**
     * Scope to filter by search term.
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }
        
        return $query;
    }
}
