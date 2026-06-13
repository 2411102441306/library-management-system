<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->string('fine_proof_path')->nullable()->after('fine_settled_at');
            $table->timestamp('fine_proof_submitted_at')->nullable()->after('fine_proof_path');
        });
    }

    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropColumn(['fine_proof_path', 'fine_proof_submitted_at']);
        });
    }
};