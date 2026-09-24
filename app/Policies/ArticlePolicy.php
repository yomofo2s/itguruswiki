<?php

namespace App\Policies;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    /** Published guides are public; drafts are visible to their author and moderators (preview). */
    public function view(?User $user, Article $article): bool
    {
        return $article->isPublished()
            || ($user && ($user->canModerate() || $article->author_id === $user->id));
    }

    public function create(User $user): bool
    {
        return $user->hasVerifiedEmail();
    }

    /** Authors can edit until their guide is published; moderators can always edit. */
    public function update(User $user, Article $article): bool
    {
        return $user->canModerate()
            || ($article->author_id === $user->id && $article->status !== ArticleStatus::Published);
    }

    public function delete(User $user, Article $article): bool
    {
        return $this->update($user, $article);
    }

    public function moderate(User $user): bool
    {
        return $user->canModerate();
    }
}
