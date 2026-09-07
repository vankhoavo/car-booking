<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_blog_only_lists_published_posts(): void
    {
        BlogPost::create([
            'title' => 'Bài đã xuất bản',
            'slug' => 'bai-da-xuat-ban',
            'content' => 'Nội dung công khai.',
            'published_at' => now()->subDay(),
            'is_published' => true,
        ]);

        BlogPost::create([
            'title' => 'Bản nháp',
            'slug' => 'ban-nhap',
            'content' => 'Nội dung nội bộ.',
            'published_at' => null,
            'is_published' => false,
        ]);

        $this->get('/blog')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('blog/Index')
                ->has('posts', 1)
                ->where('posts.0.slug', 'bai-da-xuat-ban')
            );
    }

    public function test_public_blog_cannot_open_an_unpublished_post(): void
    {
        BlogPost::create([
            'title' => 'Bản nháp',
            'slug' => 'ban-nhap',
            'content' => 'Nội dung nội bộ.',
            'is_published' => false,
        ]);

        $this->get('/blog/ban-nhap')->assertNotFound();
    }

    public function test_public_blog_can_open_a_published_post(): void
    {
        BlogPost::create([
            'title' => 'Kinh nghiệm thuê xe',
            'slug' => 'kinh-nghiem-thue-xe',
            'excerpt' => 'Một số lưu ý.',
            'content' => 'Nội dung bài viết.',
            'published_at' => now(),
            'is_published' => true,
        ]);

        $this->get('/blog/kinh-nghiem-thue-xe')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('blog/Show')
                ->where('post.slug', 'kinh-nghiem-thue-xe')
                ->where('post.title', 'Kinh nghiệm thuê xe')
            );
    }
}
