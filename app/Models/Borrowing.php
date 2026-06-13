<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Borrowing extends Model
{
    use HasFactory;

    public const ACTIVE_STATUSES = ['approved', 'overdue'];

    public const FINAL_STATUSES = ['returned', 'rejected', 'lost'];

    protected $fillable = [
        'user_id',
        'book_id',
        'borrow_date',
        'due_date',
        'loan_days',
        'penalty_amount',
        'return_date',
        'fine_proof_path',
        'fine_proof_submitted_at',
        'fine_settled_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'due_date'    => 'date',
        'return_date' => 'date',
        'loan_days'   => 'integer',
        'penalty_amount' => 'integer',
        'fine_proof_submitted_at' => 'datetime',
        'fine_settled_at' => 'datetime',
    ];

    // ─── Helpers ───────────────────────────────────────────
    public static function refreshOverdueStatuses(): int
    {
        return static::where('status', 'approved')
            ->whereDate('due_date', '<', Carbon::today())
            ->update(['status' => 'overdue']);
    }

    public function isOverdue(): bool
    {
        return in_array($this->status, self::ACTIVE_STATUSES, true)
            && $this->due_date->lt(Carbon::today());
    }

    public function getDaysLateAttribute(): int
    {
        if ($this->return_date && $this->return_date->gt($this->due_date)) {
            return (int) $this->due_date->diffInDays($this->return_date);
        }

        if (!$this->isOverdue()) {
            return 0;
        }

        return (int) $this->due_date->diffInDays(Carbon::today());
    }

    public function getFineAmountAttribute(): int
    {
        if ((int) ($this->penalty_amount ?? 0) > 0) {
            return (int) $this->penalty_amount;
        }

        if ($this->status === 'lost') {
            return (int) AppSetting::borrowingPolicy()['lost_fee'];
        }

        return $this->days_late * (int) AppSetting::borrowingPolicy()['daily_fine'];
    }

    public function getOutstandingFineAmountAttribute(): int
    {
        if ($this->isFineSettled()) {
            return 0;
        }

        return $this->fine_amount;
    }

    public function isFineSettled(): bool
    {
        return !is_null($this->fine_settled_at);
    }

    public function hasOutstandingFine(): bool
    {
        return in_array($this->status, ['returned', 'lost'], true)
            && $this->fine_amount > 0
            && !$this->isFineSettled();
    }

    public function hasFineProof(): bool
    {
        return filled($this->fine_proof_path);
    }

    public function getFineStatusLabelAttribute(): string
    {
        if ($this->hasOutstandingFine() && $this->hasFineProof()) {
            return 'Bukti terkirim';
        }

        if ($this->hasOutstandingFine()) {
            return 'Belum lunas';
        }

        if ($this->fine_amount > 0) {
            return 'Lunas';
        }

        return '-';
    }

    public function getFineStatusColorAttribute(): array
    {
        if ($this->hasOutstandingFine() && $this->hasFineProof()) {
            return ['#FEF3C7', '#B45309'];
        }

        if ($this->hasOutstandingFine()) {
            return ['#FEE2E2', '#B91C1C'];
        }

        if ($this->fine_amount > 0) {
            return ['#DCFCE7', '#15803D'];
        }

        return ['#F3F4F6', '#6B7280'];
    }

    public function getPenaltyTypeAttribute(): string
    {
        if ($this->status === 'lost') {
            return 'Hilang';
        }

        if ($this->fine_amount > 0) {
            return 'Terlambat';
        }

        return '-';
    }

    public function getDueSoonWarningAttribute(): bool
    {
        return in_array($this->status, self::ACTIVE_STATUSES, true)
            && $this->days_remaining > 0
            && $this->days_remaining <= 2;
    }

    public function getDaysRemainingAttribute(): int
    {
        if (!$this->due_date || !$this->status || !in_array($this->status, self::ACTIVE_STATUSES, true)) {
            return 0;
        }

        return max(0, (int) Carbon::today()->diffInDays($this->due_date, false));
    }

    public function getLoanPeriodLabelAttribute(): string
    {
        return $this->loan_days ? $this->loan_days . ' hari' : '-';
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