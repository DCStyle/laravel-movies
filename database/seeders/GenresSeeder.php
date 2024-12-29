<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GenresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $genres = [
            [
                'name' => 'Phim Cổ Trang',
                'slug' => 'phim-co-trang',
                'description' => '',
                'meta_title' => '',
                'meta_description' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Phim Tâm Lý',
                'slug' => 'phim-tam-ly',
                'description' => '',
                'meta_title' => '',
                'meta_description' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Phim Tình Cảm',
                'slug' => 'phim-tinh-cam',
                'description' => '',
                'meta_title' => '',
                'meta_description' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Phim Bí Ẩn',
                'slug' => 'phim-bi-an',
                'description' => '',
                'meta_title' => '',
                'meta_description' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Phim Kinh Dị',
                'slug' => 'phim-kinh-di',
                'description' => '',
                'meta_title' => '',
                'meta_description' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Phim Hành Động',
                'slug' => 'phim-hanh-dong',
                'description' => '',
                'meta_title' => '',
                'meta_description' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Phim Võ Thuật',
                'slug' => 'phim-vo-thuat',
                'description' => '',
                'meta_title' => '',
                'meta_description' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Phim Phiêu Lưu',
                'slug' => 'phim-phieu-luu',
                'description' => '',
                'meta_title' => '',
                'meta_description' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Phim Hình Sự',
                'slug' => 'phim-hinh-su',
                'description' => '',
                'meta_title' => '',
                'meta_description' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Phim Viễn Tưởng',
                'slug' => 'phim-vien-tuong',
                'description' => '',
                'meta_title' => '',
                'meta_description' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Phim Thần Thoại',
                'slug' => 'phim-than-thoai',
                'description' => '',
                'meta_title' => '',
                'meta_description' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'TV Shows',
                'slug' => 'tv-shows',
                'description' => '',
                'meta_title' => '',
                'meta_description' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Phim Gia Đình',
                'slug' => 'phim-gia-dinh',
                'description' => '',
                'meta_title' => '',
                'meta_description' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Phim Netflix',
                'slug' => 'phim-netflix',
                'description' => '',
                'meta_title' => '',
                'meta_description' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Phim Hoạt Hình',
                'slug' => 'phim-hoat-hinh',
                'description' => '',
                'meta_title' => '',
                'meta_description' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Phim BL',
                'slug' => 'phim-bl',
                'description' => '',
                'meta_title' => '',
                'meta_description' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Phim Khoa Học',
                'slug' => 'phim-khoa-hoc',
                'description' => '',
                'meta_title' => '',
                'meta_description' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Phim Âm Nhạc',
                'slug' => 'phim-am-nhac',
                'description' => '',
                'meta_title' => '',
                'meta_description' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Phim Chính Kịch',
                'slug' => 'phim-chinh-kich',
                'description' => '',
                'meta_title' => '',
                'meta_description' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('genres')->insert($genres);
    }
}