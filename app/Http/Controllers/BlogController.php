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
            'posts' => BlogPost::query()->where('is_published', true)->where(function ($query) {
                $query->whereNull('published_at')->orWhere('published_at', '<=', now());
            })->latest('published_at')->latest('id')->get(),
        ]);
    }

    public function show(BlogPost $blogPost): Response
    {
        abort_unless($blogPost->is_published && (! $blogPost->published_at || $blogPost->published_at->lte(now())), 404);

        return Inertia::render('blog/Show', ['post' => $blogPost]);
    }
}
