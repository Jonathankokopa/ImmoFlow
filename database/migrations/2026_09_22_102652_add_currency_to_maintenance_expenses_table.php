<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_expenses', function (Blueprint $table) {
            $table->string('currency', 3)
                ->nullable()
                ->after('amount')
                ->index();
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_expenses', function (Blueprint $table) {
            $table->dropIndex([
                'currency',
            ]);

            $table->dropColumn('currency');
        });
    }
};