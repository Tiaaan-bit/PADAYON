<?php

namespace App\Actions\Admin\Post;

use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;

class CreatePost
{
    public function execute(User $user, string $title, string $content): Post
    {
        return Post::create([
            'user_id' => $user->id,
            'title' => $title,
            'content' => $content,
            'published_at' => Carbon::now('Asia/Manila'),
        ]);
    }
}
