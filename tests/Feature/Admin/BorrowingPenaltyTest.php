<?php

use App\Models\AppSetting;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\User;

test('late fee multiplies by days late and lost fee stays fixed', function () {
    AppSetting::setValue('borrowing.daily_fine', 50000);
    AppSetting::setValue('borrowing.lost_fee', 250000);

    $admin = User::factory()->create(['role' => 'admin']);
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
        'name' => 'Sanksi',
        'description' => 'Kategori uji sanksi',
    ]);

    $lateBook = Book::create([
        'category_id' => $category->id,
        'title' => 'Buku Telat Uji',
        'author' => 'Tim Uji',
        'stock' => 1,
    ]);

    $lostBook = Book::create([
        'category_id' => $category->id,
        'title' => 'Buku Hilang Uji',
        'author' => 'Tim Uji',
        'stock' => 1,
    ]);

    $lateBorrowing = Borrowing::create([
        'user_id' => $member->id,
        'book_id' => $lateBook->id,
        'borrow_date' => now()->subDays(5)->toDateString(),
        'due_date' => now()->subDays(2)->toDateString(),
        'loan_days' => 3,
        'status' => 'approved',
        'notes' => null,
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.borrowings.return', $lateBorrowing))
        ->assertRedirect();

    $lateBorrowing->refresh();

    expect($lateBorrowing->status)->toBe('returned');
    expect($lateBorrowing->fine_amount)->toBe(100000);

    $lostBorrowing = Borrowing::create([
        'user_id' => $member->id,
        'book_id' => $lostBook->id,
        'borrow_date' => now()->subDays(2)->toDateString(),
        'due_date' => now()->addDays(5)->toDateString(),
        'loan_days' => 7,
        'status' => 'approved',
        'notes' => null,
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.borrowings.lost', $lostBorrowing))
        ->assertRedirect();

    $lostBorrowing->refresh();

    expect($lostBorrowing->status)->toBe('lost');
    expect($lostBorrowing->fine_amount)->toBe(250000);
});