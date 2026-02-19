<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('order_number')->unique();
            $table->string('purchaser_name');
            $table->string('purchaser_email');
            $table->string('purchaser_phone')->nullable();
            $table->string('status')->default('PENDING');
            $table->decimal('total_amount', 10, 2);
            $table->string('currency', 3)->default('GBP');
            $table->string('payment_method')->default('PAYPAL');
            $table->string('provider_order_id')->nullable();
            $table->string('provider_capture_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->foreignId('refunded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'event_id']);
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
