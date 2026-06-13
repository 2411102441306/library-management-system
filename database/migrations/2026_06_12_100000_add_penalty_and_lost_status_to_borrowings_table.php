<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowings', function ($table) {
            $table->unsignedBigInteger('penalty_amount')->default(0)->after('loan_days');
        });

        if (DB::getDriverName() === 'mysql' || DB::getDriverName() === 'mariadb') {
            DB::statement("ALTER TABLE borrowings MODIFY status ENUM('pending', 'approved', 'rejected', 'returned', 'overdue', 'lost') NOT NULL DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql' || DB::getDriverName() === 'mariadb') {
            DB::statement("ALTER TABLE borrowings MODIFY status ENUM('pending', 'approved', 'rejected', 'returned', 'overdue') NOT NULL DEFAULT 'pending'");
        }

        Schema::table('borrowings', function ($table) {
            $table->dropColumn('penalty_amount');
        });
    }
};