<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PostRequest;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PostController extends Controller
{
    public function posts(): View
    {
        $posts = Post::with('author')->whereNotNull('published_at')->latest('published_at')->paginate(10);

        return view('admin.posts', compact('posts'));
    }

    public function store(PostRequest $request): RedirectResponse
    {
        Post::create([
            'user_id' => $request->user()->id,
            'title' => $request->validated('title'),
            'content' => $request->validated('content'),
            'published_at' => \Carbon\Carbon::now('Asia/Manila'),
        ]);

        return redirect()->route('admin.posts')->with('success', 'Post created successfully.');
    }

    public function update(PostRequest $request, Post $post): RedirectResponse
    {
        $post->update([
            'title' => $request->validated('title'),
            'content' => $request->validated('content'),
        ]);

        return redirect()->route('admin.posts')->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();

        return redirect()->route('admin.posts')->with('success', 'Post deleted successfully.');
    }
}
