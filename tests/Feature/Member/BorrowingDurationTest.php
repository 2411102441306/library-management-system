<?php

use App\Models\Book;
use App\Models\Category;
use App\Models\User;

test('member can choose borrowing duration within policy range', function () {
    $member = User::factory()->create([
        'role' => 'member',
        'identity_number' => '1234567890123456',
        'phone' => '081234567891',
        'address' => 'Jl. Lengkap No. 4',
        'birth_place' => 'Samarinda',
        'birth_date' => '2000-01-01',
        'profile_photo_path' => 'profile-photos/test.jpg',
    ]);

    $category = Category::create([
        'name' => 'Durasi',
        'description' => 'Kategori pengujian durasi pinjam',
    ]);

    $book = Book::create([
        'category_id' => $category->id,
        'title' => 'Buku Durasi Uji',
        'author' => 'Tim Uji',
        'stock' => 1,
    ]);

    $this->actingAs($member)
        ->post(route('member.catalog.borrow', $book), [
            'notes' => 'Pinjam 3 hari',
            'loan_days' => 3,
        ])
        ->assertRedirect(route('member.history'));

    $this->assertDatabaseHas('borrowings', [
        'user_id' => $member->id,
        'book_id' => $book->id,
        'loan_days' => 3,
        'status' => 'pending',
    ]);
});