<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->timestamp('start_at');
            $table->timestamp('end_at')->nullable();
            $table->string('location')->nullable();
            $table->string('location_address')->nullable();
            $table->string('event_type')->default('FREE');
            $table->string('status')->default('draft');
            $table->string('flyer_path')->nullable();
            $table->longText('body_html')->nullable();
            $table->text('short_description')->nullable();
            $table->integer('rsvp_capacity')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->unique(['tenant_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
