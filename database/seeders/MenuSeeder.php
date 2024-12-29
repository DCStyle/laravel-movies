<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run()
    {
        // Root menu items
        $mainMenus = [
            ['title' => 'Phim Lẻ', 'url' => '/phim-le'],
            ['title' => 'Phim Bộ', 'url' => '/phim-bo'],
            ['title' => 'Thể Loại', 'url' => '#', 'children' => [
                ['title' => 'Phim Cổ Trang', 'url' => '/the-loai/phim-co-trang'],
                ['title' => 'Phim Bí Ẩn', 'url' => '/the-loai/phim-bi-an'],
                ['title' => 'Phim Võ Thuật', 'url' => '/the-loai/phim-vo-thuat'],
                ['title' => 'Phim Phiêu Lưu', 'url' => '/the-loai/phim-phieu-luu'],
                ['title' => 'Phim Hình Sự', 'url' => '/the-loai/phim-hinh-su'],
                ['title' => 'Phim Viễn Tưởng', 'url' => '/the-loai/phim-vien-tuong'],
                ['title' => 'Phim Thần Thoại', 'url' => '/the-loai/phim-than-thoai'],
                ['title' => 'TV Shows', 'url' => '/the-loai/tv-shows'],
            ]],
            ['title' => 'Quốc Gia', 'url' => '#', 'children' => [
                ['title' => 'Phim Âu Mỹ', 'url' => '/quoc-gia/phim-au-my'],
                ['title' => 'Phim Nhật Bản', 'url' => '/quoc-gia/phim-nhat-ban'],
                ['title' => 'Phim Thái Lan', 'url' => '/quoc-gia/phim-thai-lan'],
                ['title' => 'Phim Indonesia', 'url' => '/quoc-gia/phim-indonesia'],
                ['title' => 'Phim Hàn Quốc', 'url' => '/quoc-gia/phim-han-quoc'],
                ['title' => 'Phim Đài Loan', 'url' => '/quoc-gia/phim-dai-loan'],
                ['title' => 'Phim Ấn Độ', 'url' => '/quoc-gia/phim-an-do'],
                ['title' => 'Phim Singapore', 'url' => '/quoc-gia/phim-singapore'],
                ['title' => 'Phim Trung Quốc', 'url' => '/quoc-gia/phim-trung-quoc'],
                ['title' => 'Phim Hồng Kông', 'url' => '/quoc-gia/phim-hong-kong'],
                ['title' => 'Phim Philippines', 'url' => '/quoc-gia/phim-philippines'],
            ]],
            ['title' => 'Nhóm', 'url' => '/nhom'],
        ];

        // Helper function to insert menu items recursively
        $this->createMenuItems($mainMenus);
    }

    private function createMenuItems(array $menus, $parentId = null)
    {
        foreach ($menus as $menuData) {
            $children = $menuData['children'] ?? [];
            unset($menuData['children']);

            $menu = Menu::create([
                'title' => $menuData['title'],
                'url' => $menuData['url'],
                'parent_id' => $parentId,
                'order' => 0, // You can customize order if needed
            ]);

            if (!empty($children)) {
                $this->createMenuItems($children, $menu->id);
            }
        }
    }
}
