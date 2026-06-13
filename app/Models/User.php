<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'identity_number',
        'birth_place',
        'birth_date',
        'profile_photo_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'birth_date'        => 'date',
            'password'          => 'hashed',
        ];
    }

    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            if (!$this->profile_photo_path) {
                return null;
            }

            return Storage::disk('public')->url($this->profile_photo_path);
        });
    }

    // ─── Helpers ───────────────────────────────────────────
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isMember(): bool
    {
        return $this->role === 'member';
    }

    public function hasBorrowerProfile(): bool
    {
        return filled($this->name)
            && filled($this->email)
            && filled($this->identity_number)
            && filled($this->phone)
            && filled($this->address)
            && filled($this->birth_place)
            && filled($this->birth_date)
            && filled($this->profile_photo_path);
    }

    public function missingBorrowerProfileFields(): array
    {
        return collect([
            'name' => 'nama asli',
            'identity_number' => 'NIK / nomor identitas',
            'phone' => 'nomor telepon',
            'address' => 'alamat',
            'birth_place' => 'tempat lahir',
            'birth_date' => 'tanggal lahir',
            'profile_photo_path' => 'foto profil',
        ])
            ->filter(fn (string $label, string $field) => blank($this->{$field}))
            ->values()
            ->all();
    }

    // ─── Relationships ──────────────────────────────────────
    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    public function activeBorrowings()
    {
        return $this->hasMany(Borrowing::class)
                    ->whereIn('status', ['approved', 'overdue']);
    }
}