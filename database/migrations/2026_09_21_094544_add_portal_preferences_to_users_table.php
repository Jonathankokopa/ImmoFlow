<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            if (!Schema::hasColumn('users', 'profile_photo')) {
                $table->string('profile_photo')
                    ->nullable()
                    ->after('password');
            }

            if (!Schema::hasColumn('users', 'appearance')) {
                $table->string('appearance')
                    ->default('light')
                    ->after('profile_photo');
            }

        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            if (Schema::hasColumn('users', 'appearance')) {
                $table->dropColumn('appearance');
            }

            if (Schema::hasColumn('users', 'profile_photo')) {
                $table->dropColumn('profile_photo');
            }

        });
    }
};