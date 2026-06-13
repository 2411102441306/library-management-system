<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('identity_number', 32)->nullable()->unique()->after('address');
            $table->string('birth_place', 120)->nullable()->after('address');
            $table->date('birth_date')->nullable()->after('birth_place');
            $table->string('profile_photo_path')->nullable()->after('birth_date');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'identity_number',
                'birth_place',
                'birth_date',
                'profile_photo_path',
            ]);
        });
    }
};