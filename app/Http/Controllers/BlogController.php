<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('blog/Index', [
            'posts' => BlogPost::query()
                ->where('is_published', true)
                ->where(function ($query) {
                    $query->whereNull('published_at')
                        ->orWhere('published_at', '<=', now());
                })
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->get(['id', 'title', 'slug', 'excerpt', 'image', 'published_at']),
        ]);
    }

    public function show(string $slug): Response
    {
        $post = BlogPost::query()
            ->where('is_published', true)
            ->where(function ($query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->where('slug', $slug)
            ->firstOrFail();

        return Inertia::render('blog/Show', ['post' => $post]);
    }
}
