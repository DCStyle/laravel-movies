<?php

namespace App\Policies;

use App\Models\Movie;
use App\Models\User;

class MoviePolicy
{
    public function update(User $user, Movie $movie)
    {
        return $user->id === $movie->user_id || $user->hasRole('Admin');
    }

    public function delete(User $user, Movie $movie)
    {
        return $user->hasRole('Admin') || ($user->id === $movie->user_id && $user->hasRole('Moderator'));
    }
}