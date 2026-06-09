<?php

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\User;

test('admin can delete a book that only has completed borrowings', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $category = Category::create([
        'name' => 'Fiksi',
        'description' => 'Kategori fiksi',
    ]);

    $book = Book::create([
        'category_id' => $category->id,
        'title' => 'Buku Uji Hapus',
        'author' => 'Penulis Uji',
        'stock' => 0,
    ]);

    $borrowing = Borrowing::create([
        'user_id' => $admin->id,
        'book_id' => $book->id,
        'borrow_date' => now()->subDays(10)->toDateString(),
        'due_date' => now()->subDays(3)->toDateString(),
        'return_date' => now()->subDays(4)->toDateString(),
        'status' => 'returned',
        'notes' => null,
    ]);

    $response = $this
        ->actingAs($admin)
        ->delete(route('admin.books.destroy', $book));

    $response
        ->assertRedirect(route('admin.books.index'))
        ->assertSessionHas('success', 'Buku berhasil dihapus.');

    $this->assertDatabaseMissing('books', ['id' => $book->id]);
    $this->assertDatabaseMissing('borrowings', ['id' => $borrowing->id]);
});

test('admin cannot delete a book that still has active borrowings', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $category = Category::create([
        'name' => 'Non-Fiksi',
        'description' => 'Kategori non-fiksi',
    ]);

    $book = Book::create([
        'category_id' => $category->id,
        'title' => 'Buku Uji Aktif',
        'author' => 'Penulis Uji',
        'stock' => 0,
    ]);

    Borrowing::create([
        'user_id' => $admin->id,
        'book_id' => $book->id,
        'borrow_date' => now()->subDays(2)->toDateString(),
        'due_date' => now()->addDays(5)->toDateString(),
        'return_date' => null,
        'status' => 'approved',
        'notes' => null,
    ]);

    $response = $this
        ->actingAs($admin)
        ->from(route('admin.books.index'))
        ->delete(route('admin.books.destroy', $book));

    $response
        ->assertRedirect(route('admin.books.index'))
        ->assertSessionHas('error', 'Buku tidak dapat dihapus karena masih memiliki peminjaman aktif.');

    $this->assertDatabaseHas('books', ['id' => $book->id]);
});