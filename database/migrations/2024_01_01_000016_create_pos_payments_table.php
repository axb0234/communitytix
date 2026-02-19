<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('method')->default('CARD');
            $table->string('reference')->nullable();
            $table->string('status')->default('PENDING_RECONCILIATION');
            $table->foreignId('recorded_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('recorded_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_payments');
    }
};
