<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $books = [
            // Fiksi
            ['category' => 'Fiksi', 'title' => 'Laskar Pelangi',         'author' => 'Andrea Hirata',       'stock' => 4],
            ['category' => 'Fiksi', 'title' => 'Bumi Manusia',           'author' => 'Pramoedya Ananta Toer','stock' => 3],
            ['category' => 'Fiksi', 'title' => 'Dilan 1990',             'author' => 'Pidi Baiq',            'stock' => 2],
            ['category' => 'Fiksi', 'title' => 'Dune',                   'author' => 'Frank Herbert',        'stock' => 3],
            ['category' => 'Fiksi', 'title' => '1984',                   'author' => 'George Orwell',        'stock' => 0],
            // Non-Fiksi
            ['category' => 'Non-Fiksi', 'title' => 'Atomic Habits',      'author' => 'James Clear',          'stock' => 0],
            ['category' => 'Non-Fiksi', 'title' => 'Sapiens',            'author' => 'Yuval Noah Harari',    'stock' => 2],
            ['category' => 'Non-Fiksi', 'title' => 'Ikigai',             'author' => 'Héctor García',        'stock' => 3],
            // Teknologi
            ['category' => 'Teknologi', 'title' => 'Clean Code',         'author' => 'Robert C. Martin',     'stock' => 5],
            ['category' => 'Teknologi', 'title' => 'The Pragmatic Programmer', 'author' => 'David Thomas',   'stock' => 1],
            ['category' => 'Teknologi', 'title' => 'You Don\'t Know JS', 'author' => 'Kyle Simpson',         'stock' => 2],
            // Sejarah
            ['category' => 'Sejarah', 'title' => 'Sapiens: Riwayat Singkat Umat Manusia', 'author' => 'Yuval Noah Harari', 'stock' => 2],
            ['category' => 'Sejarah', 'title' => 'Guns, Germs, and Steel','author' => 'Jared Diamond',       'stock' => 1],
            // Ekonomi
            ['category' => 'Ekonomi', 'title' => 'Rich Dad Poor Dad',    'author' => 'Robert Kiyosaki',      'stock' => 3],
            ['category' => 'Ekonomi', 'title' => 'The Alchemist',        'author' => 'Paulo Coelho',         'stock' => 2],
        ];

        foreach ($books as $b) {
            $category = Category::where('name', $b['category'])->first();
            if (!$category) continue;

            Book::create([
                'category_id'    => $category->id,
                'title'          => $b['title'],
                'author'         => $b['author'],
                'stock'          => $b['stock'],
                'published_year' => rand(2000, 2023),
                'description'    => 'Deskripsi buku ' . $b['title'] . ' oleh ' . $b['author'] . '.',
            ]);
        }
    }
}