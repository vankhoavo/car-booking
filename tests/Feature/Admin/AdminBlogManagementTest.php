<?php

namespace Tests\Feature\Admin;

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBlogManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    public function test_admin_can_view_blog_management(): void
    {
        $this->actingAs($this->admin())->get('/admin/blog')->assertOk();
    }

    public function test_admin_can_create_blog_post(): void
    {
        $this->actingAs($this->admin())->post('/admin/blog', [
            'title' => 'Hoi An Travel Guide',
            'slug' => '',
            'excerpt' => 'Gợi ý lịch trình.',
            'content' => 'Nội dung bài viết.',
            'image' => null,
            'published_at' => null,
            'is_published' => true,
        ])->assertRedirect();

        $this->assertDatabaseHas('blog_posts', [
            'title' => 'Hoi An Travel Guide',
            'slug' => 'hoi-an-travel-guide',
            'is_published' => true,
        ]);
    }

    public function test_non_admin_cannot_manage_blog(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'user']))
            ->get('/admin/blog')
            ->assertForbidden();
    }
}
