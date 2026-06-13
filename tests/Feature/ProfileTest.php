<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'identity_number' => '1234567890123456',
            'phone' => '081234567890',
            'address' => 'Jl. Test No. 1',
            'birth_place' => 'Samarinda',
            'birth_date' => '2000-01-01',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('Test User', $user->name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertSame('1234567890123456', $user->identity_number);
    $this->assertSame('081234567890', $user->phone);
    $this->assertSame('Jl. Test No. 1', $user->address);
    $this->assertSame('Samarinda', $user->birth_place);
    $this->assertSame('2000-01-01', $user->birth_date->format('Y-m-d'));
    $this->assertNull($user->email_verified_at);
});

test('profile photo can be uploaded and removed', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    $this->actingAs($user)->patch('/profile', [
        'name' => $user->name,
        'email' => $user->email,
        'identity_number' => '1234567890123456',
        'phone' => '081234567890',
        'address' => 'Jl. Test No. 2',
        'birth_place' => 'Samarinda',
        'birth_date' => '2000-01-01',
        'profile_photo' => UploadedFile::fake()->create('profile.jpg', 100, 'image/jpeg'),
    ])->assertRedirect('/profile');

    $user->refresh();
    $photoPath = $user->profile_photo_path;

    Storage::disk('public')->assertExists($photoPath);

    $this->actingAs($user)->patch('/profile', [
        'name' => $user->name,
        'email' => $user->email,
        'identity_number' => '1234567890123456',
        'phone' => '081234567890',
        'address' => 'Jl. Test No. 2',
        'birth_place' => 'Samarinda',
        'birth_date' => '2000-01-01',
        'remove_profile_photo' => 1,
    ])->assertRedirect('/profile');

    $removedPhotoPath = $photoPath;
    $photoPath = $user->refresh()->profile_photo_path;

    expect($photoPath)->toBeNull();
    Storage::disk('public')->assertMissing($removedPhotoPath);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => $user->email,
            'identity_number' => '1234567890123456',
            'phone' => '081234567890',
            'address' => 'Jl. Test No. 3',
            'birth_place' => 'Samarinda',
            'birth_date' => '2000-01-01',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($user->fresh());
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('userDeletion', 'password')
        ->assertRedirect('/profile');

    $this->assertNotNull($user->fresh());
});
