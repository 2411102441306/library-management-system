<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'author',
        'publisher',
        'isbn',
        'published_year',
        'description',
        'stock',
        'cover_image',
        'cover_url',
    ];

    // ─── Helpers ───────────────────────────────────────────
    public function isAvailable(): bool
    {
        return $this->stock > 0;
    }

    public function getCoverUrlAttribute(): string
    {
        if ($this->cover_image) {
            return asset('storage/' . $this->cover_image);
        }

        if (!empty($this->attributes['cover_url'])) {
            return $this->attributes['cover_url'];
        }

        return asset('images/default-cover.png');
    }

    // ─── Relationships ──────────────────────────────────────
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }
}