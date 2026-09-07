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
                    $query->whereNull('published_at')->orWhere('published_at', '<=', now());
                })
                ->latest('published_at')
                ->latest('id')
                ->get(),
        ]);
    }

    public function show(string $slug): Response
    {
        $blogPost = BlogPost::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->where(function ($query) {
                $query->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->firstOrFail();

        return Inertia::render('blog/Show', ['post' => $blogPost]);
    }
}
