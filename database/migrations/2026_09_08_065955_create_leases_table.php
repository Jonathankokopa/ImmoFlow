<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leases', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->foreignId('unit_id')
                ->constrained('units')
                ->restrictOnDelete();

            $table->date('start_date');
            $table->date('end_date');

            // Rent is captured on the lease so future unit changes
            // do not alter existing lease terms.
            $table->decimal('monthly_rent', 12, 2);
            $table->string('currency', 3)->default('USD');

            $table->decimal('security_deposit', 12, 2)->default(0);

            $table->unsignedTinyInteger('payment_due_day')->default(5);

            $table->enum('status', [
                'active',
                'expired',
                'terminated',
                'pending',
            ])->default('pending');

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leases');
    }
};