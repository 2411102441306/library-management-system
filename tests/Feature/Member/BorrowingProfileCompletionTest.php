<?php

use App\Models\Book;
use App\Models\Category;
use App\Models\User;

test('member must complete profile before borrowing', function () {
    $member = User::factory()->create([
        'role' => 'member',
        'identity_number' => null,
        'phone' => null,
        'address' => null,
        'birth_place' => null,
        'birth_date' => null,
        'profile_photo_path' => null,
    ]);

    $category = Category::create([
        'name' => 'Uji Coba',
        'description' => 'Kategori untuk pengujian',
    ]);

    $book = Book::create([
        'category_id' => $category->id,
        'title' => 'Buku Peminjaman Terproteksi',
        'author' => 'Tim Uji',
        'stock' => 1,
    ]);

    $this->actingAs($member)
        ->from(route('member.catalog.show', $book))
        ->post(route('member.catalog.borrow', $book), [
            'notes' => 'Ingin meminjam buku ini',
        ])
        ->assertRedirect(route('member.catalog.show', $book));

    $this->assertDatabaseMissing('borrowings', [
        'user_id' => $member->id,
        'book_id' => $book->id,
    ]);

    $member->update([
        'identity_number' => '1234567890123456',
        'phone' => '081234567891',
        'address' => 'Jl. Lengkap No. 4',
        'birth_place' => 'Samarinda',
        'birth_date' => '2000-01-01',
        'profile_photo_path' => 'profile-photos/test.jpg',
    ]);

    $this->actingAs($member)
        ->post(route('member.catalog.borrow', $book), [
            'notes' => 'Sekarang profil sudah lengkap',
        ])
        ->assertRedirect(route('member.history'));

    $this->assertDatabaseHas('borrowings', [
        'user_id' => $member->id,
        'book_id' => $book->id,
        'status' => 'pending',
    ]);
});

test('member without profile photo cannot borrow', function () {
    $member = User::factory()->create([
        'role' => 'member',
        'identity_number' => '1234567890123456',
        'phone' => '081234567891',
        'address' => 'Jl. Lengkap No. 4',
        'birth_place' => 'Samarinda',
        'birth_date' => '2000-01-01',
        'profile_photo_path' => null,
    ]);

    $category = Category::create([
        'name' => 'Uji Coba 2',
        'description' => 'Kategori untuk pengujian foto profil',
    ]);

    $book = Book::create([
        'category_id' => $category->id,
        'title' => 'Buku Foto Profil Wajib',
        'author' => 'Tim Uji',
        'stock' => 1,
    ]);

    $this->actingAs($member)
        ->from(route('member.catalog.show', $book))
        ->post(route('member.catalog.borrow', $book), [
            'notes' => 'Coba pinjam tanpa foto',
        ])
        ->assertRedirect(route('member.catalog.show', $book));

    $this->assertDatabaseMissing('borrowings', [
        'user_id' => $member->id,
        'book_id' => $book->id,
    ]);
});