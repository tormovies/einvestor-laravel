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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('wp_id')->nullable()->index();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('content');
            $table->text('excerpt')->nullable();
            $table->enum('status', ['publish', 'draft', 'private'])->default('publish');
            $table->foreignId('parent_id')->nullable()->constrained('pages');
            $table->integer('menu_order')->default(0);
            $table->foreignId('author_id')->nullable()->constrained('users');
            $table->unsignedBigInteger('featured_image_id')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('slug');
            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
