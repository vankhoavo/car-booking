<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if (! User::query()->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        foreach ([
            ['name' => 'Toyota Vios', 'brand' => 'Toyota', 'model' => 'Vios', 'type' => 'Sedan', 'seats' => 5, 'description' => 'Xe sedan tiết kiệm, phù hợp di chuyển nội thành và đường dài.', 'image' => null, 'price' => 650000, 'status' => 'available'],
            ['name' => 'Toyota Innova', 'brand' => 'Toyota', 'model' => 'Innova', 'type' => 'MPV', 'seats' => 7, 'description' => 'Không gian rộng, phù hợp gia đình và nhóm nhỏ.', 'image' => null, 'price' => 900000, 'status' => 'available'],
            ['name' => 'Kia Carnival', 'brand' => 'Kia', 'model' => 'Carnival', 'type' => 'MPV cao cấp', 'seats' => 7, 'description' => 'Không gian cao cấp, thoải mái cho chuyến đi dài.', 'image' => null, 'price' => 1450000, 'status' => 'available'],
            ['name' => 'VinFast VF 9', 'brand' => 'VinFast', 'model' => 'VF 9', 'type' => 'SUV điện', 'seats' => 7, 'description' => 'SUV điện cao cấp, vận hành êm ái.', 'image' => null, 'price' => 1800000, 'status' => 'available'],
        ] as $vehicle) {
            Vehicle::updateOrCreate(['name' => $vehicle['name']], $vehicle);
        }

        foreach ([
            ['slug' => 'kinh-nghiem-thue-xe-theo-ngay', 'title' => 'Kinh nghiệm thuê xe theo ngày', 'excerpt' => 'Những điều nên kiểm tra trước khi chọn xe và gửi yêu cầu thuê.', 'content' => 'Kiểm tra số chỗ, thời gian thuê, điểm nhận trả và tình trạng xe. Hãy gửi thông tin đầy đủ để chúng tôi tư vấn và xác nhận lịch xe phù hợp.', 'image' => null, 'published_at' => now()->toDateString(), 'is_published' => true],
            ['slug' => 'cach-chon-xe-cho-chuyen-di-gia-dinh', 'title' => 'Cách chọn xe cho chuyến đi gia đình', 'excerpt' => 'Ưu tiên số chỗ, hành lý và sự thoải mái để chuyến đi nhẹ nhàng hơn.', 'content' => 'Nhóm đông nên ưu tiên xe 7 chỗ. Nếu mang nhiều hành lý, nên chọn không gian cabin rộng và xác nhận trước nhu cầu với đơn vị cung cấp dịch vụ.', 'image' => null, 'published_at' => now()->subDay()->toDateString(), 'is_published' => true],
        ] as $post) {
            BlogPost::updateOrCreate(['slug' => $post['slug']], $post);
        }
    }
}
