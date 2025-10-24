<?php

namespace App\Policies;

use App\Models\BlogComment;
use App\Models\User;

class BlogCommentPolicy
{
    /**
     * Determine whether the user can delete the comment.
     */
    public function delete(User $user, BlogComment $comment): bool
    {
        return $user->id === $comment->user_id || $user->isAdmin();
    }
}
