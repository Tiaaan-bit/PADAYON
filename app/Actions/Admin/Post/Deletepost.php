<?php

namespace App\Actions\Admin\Post;

use App\Models\Post;

class DeletePost
{
    public function execute(Post $post): bool
    {
        return $post->delete();
    }
}
