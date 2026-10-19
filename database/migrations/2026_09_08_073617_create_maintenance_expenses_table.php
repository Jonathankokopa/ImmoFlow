<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('maintenance_expenses', function (Blueprint $table) {
        $table->id();

        $table->foreignId('maintenance_request_id')
            ->constrained('maintenance_requests')
            ->cascadeOnDelete();

        $table->enum('expense_type', [
            'labour',
            'materials',
            'parts',
            'other',
        ]);

        $table->string('description');
        $table->decimal('amount', 12, 2);
        $table->date('expense_date');

        $table->text('notes')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_expenses');
    }
};
