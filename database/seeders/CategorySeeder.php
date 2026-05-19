<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Fiksi',     'description' => 'Novel, cerpen, dan karya fiksi imajinatif'],
            ['name' => 'Non-Fiksi', 'description' => 'Buku berbasis fakta, memoar, dan esai'],
            ['name' => 'Sains',     'description' => 'Ilmu pengetahuan alam, biologi, fisika, kimia'],
            ['name' => 'Sejarah',   'description' => 'Catatan sejarah, biografi tokoh, dan peradaban'],
            ['name' => 'Teknologi', 'description' => 'Pemrograman, rekayasa perangkat lunak, AI'],
            ['name' => 'Sastra',    'description' => 'Puisi, drama, dan karya sastra klasik'],
            ['name' => 'Filsafat',  'description' => 'Pemikiran, etika, logika, dan epistemologi'],
            ['name' => 'Ekonomi',   'description' => 'Keuangan, bisnis, manajemen, dan investasi'],
        ];

        foreach ($categories as $cat) {
            Category::create([
                'name'        => $cat['name'],
                'slug'        => Str::slug($cat['name']),
                'description' => $cat['description'],
            ]);
        }
    }
}