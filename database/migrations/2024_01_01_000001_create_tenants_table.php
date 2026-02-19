<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('tagline')->nullable();
            $table->string('currency', 3)->default('GBP');
            $table->string('timezone')->default('Europe/London');
            $table->string('account_type')->default('free');
            $table->boolean('tenant_active')->default(true);
            $table->timestamp('sub_start_date_utc')->nullable();
            $table->timestamp('sub_end_date_utc')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
