<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;


class CommentPolicy
{
    use HandlesAuthorization;


    public function view(User $user, Comment $comment): Response|bool
    {
        return true;
    }

    public function delete(User $user, Comment $comment): Response|bool
    {
        return true;
    }
}
