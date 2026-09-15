<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isEditor() || $user->isPenulis();
    }

    /**
     * Dashboard/management view — not public read.
     * Admin/editor: all articles. Penulis: own articles only.
     * Other authenticated users: published articles only.
     */
    public function view(User $user, Article $article): bool
    {
        if ($user->isAdmin() || $user->isEditor()) {
            return true;
        }

        if ($user->isPenulis()) {
            return $user->id === $article->author_id;
        }

        return $article->status === 'published' && $article->published_at !== null;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isPenulis() || $user->isEditor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Article $article): bool
    {
        if ($user->isAdmin() || $user->isEditor()) {
            return true;
        }

        if ($user->isPenulis()) {
            return $user->id === $article->author_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Article $article): bool
    {
        if ($user->isAdmin() || $user->isEditor()) {
            return true;
        }

        if ($user->isPenulis()) {
            return $user->id === $article->author_id;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Article $article): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Article $article): bool
    {
        return false;
    }
}
