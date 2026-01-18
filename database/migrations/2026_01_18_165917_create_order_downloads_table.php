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
        Schema::create('order_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('order_item_id')->constrained('order_items')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->string('email');
            $table->string('download_token')->unique();
            $table->integer('download_count')->default(0);
            $table->integer('download_limit')->default(5);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->index('order_id');
            $table->index('user_id');
            $table->index('email');
            $table->index('download_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_downloads');
    }
};
