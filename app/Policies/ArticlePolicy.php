<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    /**
     * Determine whether the user can view any models.
     * Editor may list/view for pending review; full CRUD is admin-only via routes.
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
     * Create: admin (own articles) or penulis. Editor only reviews — no create.
     */
    public function create(User $user): bool
    {
        return $user->isPenulis() || $user->isAdmin();
    }

    /**
     * Update content/metadata: only the article owner (admin or penulis).
     * Admin may not edit penulis (or other authors') articles.
     * Editor moderates via approve/reject routes (not this ability).
     */
    public function update(User $user, Article $article): bool
    {
        if ($user->isAdmin() || $user->isPenulis()) {
            return (int) $user->id === (int) $article->author_id;
        }

        return false;
    }

    /**
     * Curate flags (featured / breaking): admin & editor may curate any article.
     * Owner may also curate their own (keeps existing penulis controls if any).
     */
    public function curate(User $user, Article $article): bool
    {
        if ($user->isAdmin() || $user->isEditor()) {
            return true;
        }

        return $this->update($user, $article);
    }

    /**
     * Suspend / unsuspend public display — admin & editor moderation.
     */
    public function suspend(User $user, Article $article): bool
    {
        return $user->isAdmin() || $user->isEditor();
    }

    /**
     * Delete: only the article owner (admin or penulis). Editor cannot delete.
     */
    public function delete(User $user, Article $article): bool
    {
        if ($user->isAdmin() || $user->isPenulis()) {
            return (int) $user->id === (int) $article->author_id;
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
