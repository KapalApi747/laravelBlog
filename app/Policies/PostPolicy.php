<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    /**
     * Bepaal of de gebruiker alle posts mag bekijken
     * Admin en authors mogen alles zien
     * Subscribers mogen posts enkel bekijken
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('author') || $user->hasRole('subscriber');
    }

    /**
     * Bepalen of de gebruiker een SPECIFIEKE post mag bekijken.
     * Iedereen mag posts bekijken.
     */
    public function view(User $user, Post $post): bool
    {
        return $user->hasRole('admin') || $user->hasRole('author') || $user->hasRole('subscriber');
    }

    /**
     * Alleen admin en author mogen posts aanmaken.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('author');
    }

    /**
     * Wie mag bewerken?
     * Admins mogen alle posts bewerken.
     * Authors mogen enkel hun eigen posts bewerken.
     */
    public function update(User $user, Post $post): bool
    {
        return $user->hasRole('admin') || $user->id === $post->author_id;
    }

    /**
     * Admins mogen alles verwijderen.
     * Authors enkel hun eigen posts.
     */
    public function delete(User $user, Post $post): bool
    {
        return $user->hasRole('admin') || $user->id === $post->author_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Post $post): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Post $post): bool
    {
        return $user->hasRole('admin');
    }
}
