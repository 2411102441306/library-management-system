<?php

use App\Models\AppSetting;
use App\Models\Book;
use App\Models\Category;
use App\Models\User;

test('admin can update borrowing policy and it affects new borrowings', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->put(route('admin.settings.update'), [
            'policy_section' => 1,
            'default_days' => 5,
            'min_days' => 3,
            'max_days' => 7,
            'daily_fine' => 2500,
            'lost_fee' => 250000,
        ])
        ->assertRedirect();

    expect(AppSetting::borrowingPolicy())->toMatchArray([
        'default_days' => 5,
        'min_days' => 3,
        'max_days' => 7,
        'daily_fine' => 2500,
            'lost_fee' => 250000,
    ]);

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
        'name' => 'Pengaturan',
        'description' => 'Kategori uji pengaturan',
    ]);

    $book = Book::create([
        'category_id' => $category->id,
        'title' => 'Buku Aturan Uji',
        'author' => 'Tim Uji',
        'stock' => 1,
    ]);

    $this->actingAs($member)
        ->post(route('member.catalog.borrow', $book), [
            'notes' => 'Gunakan durasi default baru',
        ])
        ->assertRedirect(route('member.history'));

    $this->assertDatabaseHas('borrowings', [
        'user_id' => $member->id,
        'book_id' => $book->id,
        'loan_days' => 5,
        'status' => 'pending',
    ]);
});