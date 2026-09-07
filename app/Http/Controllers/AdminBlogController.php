<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AdminBlogController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/Blog', [
            'posts' => BlogPost::query()->latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        BlogPost::create($this->validated($request));

        return back()->with('success', 'Đã tạo bài viết.');
    }

    public function update(Request $request, BlogPost $blogPost): RedirectResponse
    {
        $blogPost->update($this->validated($request, $blogPost));

        return back()->with('success', 'Đã cập nhật bài viết.');
    }

    public function destroy(BlogPost $blogPost): RedirectResponse
    {
        $blogPost->delete();

        return back()->with('success', 'Đã xóa bài viết.');
    }

    private function validated(Request $request, ?BlogPost $post = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:190'],
            'slug' => ['nullable', 'string', 'max:190'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'image' => ['nullable', 'url', 'max:500'],
            'published_at' => ['nullable', 'date'],
            'is_published' => ['required', 'boolean'],
        ]);

        $slug = trim((string) ($data['slug'] ?? ''));
        $data['slug'] = Str::slug($slug !== '' ? $slug : $data['title']);

        $query = BlogPost::query()->where('slug', $data['slug']);
        if ($post) {
            $query->whereKeyNot($post->getKey());
        }

        if ($query->exists()) {
            $data['slug'] .= '-' . Str::lower(Str::random(5));
        }

        if ($data['is_published'] && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        return $data;
    }
}
