<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Borrowing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'borrow_date',
        'due_date',
        'return_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'due_date'    => 'date',
        'return_date' => 'date',
    ];

    // ─── Helpers ───────────────────────────────────────────
    public function isOverdue(): bool
    {
        return $this->status === 'approved'
            && $this->due_date->isPast();
    }

    public function getDaysLateAttribute(): int
    {
        if (!$this->isOverdue()) return 0;
        return (int) $this->due_date->diffInDays(Carbon::today());
    }

    // ─── Relationships ──────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}