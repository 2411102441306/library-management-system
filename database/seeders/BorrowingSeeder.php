<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Database\Seeder;

class BorrowingSeeder extends Seeder
{
    public function run(): void
    {
        $members = User::where('role', 'member')->get();
        $books   = Book::where('stock', '>', 0)->get();

        $statuses = ['approved', 'returned', 'overdue', 'pending'];

        foreach ($members->take(6) as $index => $member) {
            $book        = $books[$index % $books->count()];
            $borrowDate  = now()->subDays(rand(5, 30));
            $dueDate     = $borrowDate->copy()->addDays(7);
            $status      = $statuses[$index % count($statuses)];
            $returnDate  = $status === 'returned'
                ? $dueDate->copy()->subDays(rand(0, 3))
                : null;

            Borrowing::create([
                'user_id'     => $member->id,
                'book_id'     => $book->id,
                'borrow_date' => $borrowDate->toDateString(),
                'due_date'    => $dueDate->toDateString(),
                'return_date' => $returnDate?->toDateString(),
                'status'      => $status,
                'notes'       => null,
            ]);
        }
    }
}