<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Post\CreatePost;
use App\Actions\Admin\Post\DeletePost;
use App\Actions\Admin\Post\UpdatePost;
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

    public function store(PostRequest $request, CreatePost $createPost): RedirectResponse
    {
        $this->authorize('create', Post::class);

        $createPost->execute($request->user(), $request->validated('title'), $request->validated('content'));

        return redirect()->route('admin.posts')->with('success', 'Post created successfully.');
    }

    public function update(PostRequest $request, Post $post, UpdatePost $updatePost): RedirectResponse
    {
        $this->authorize('update', $post);

        $updatePost->execute($post, $request->validated('title'), $request->validated('content'));

        return redirect()->route('admin.posts')->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post, DeletePost $deletePost): RedirectResponse
    {
        $this->authorize('delete', $post);

        $deletePost->execute($post);

        return redirect()->route('admin.posts')->with('success', 'Post deleted successfully.');
    }
}
