<?php 

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImagePost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImagePostController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', ImagePost::class);

        $validated = $request->validateWithBag('imagePost', [
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $path = $validated['image']->store('image-posts', 'public');

        ImagePost::create([
            'author_id' => $request->user()->id,
            'image_path' => $path,
            'published_at' => now('Asia/Manila'),
        ]);

        return redirect()->route('admin.posts')->with('success', 'Image post published successfully.');
    }

    public function update(Request $request, ImagePost $imagePost): RedirectResponse
    {
        $this->authorize('update', $imagePost);

        $validated = $request->validateWithBag('imagePost', [
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $newPath = $validated['image']->store('image-posts', 'public');

        $oldPath = $imagePost->image_path;

        $imagePost->update([
            'image_path' => $newPath,
        ]);

        Storage::disk('public')->delete($oldPath);

        return redirect()->route('admin.posts')->with('success', 'Image post updated successfully.');
    }

    public function destroy(ImagePost $imagePost): RedirectResponse
    {
        $this->authorize('delete', $imagePost);

        $path = $imagePost->image_path;

        $imagePost->delete();

        Storage::disk('public')->delete($path);

        return redirect()->route('admin.posts')->with('success', 'Image post deleted successfully.');
    }
}
