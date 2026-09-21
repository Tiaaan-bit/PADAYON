<?php

namespace App\Actions\Admin\Post;

use App\Models\Post;

class UpdatePost
{
    public function execute(Post $post, string $title, string $content): Post
    {
        $post->update([
            'title' => $title,
            'content' => $content,
        ]);

        return $post->refresh();
    }
}
