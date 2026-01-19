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
        Schema::table('posts', function (Blueprint $table) {
            $table->string('seo_title', 255)->nullable()->after('title');
            $table->text('seo_description')->nullable()->after('seo_title');
            $table->string('seo_h1', 255)->nullable()->after('seo_description');
            $table->text('seo_intro_text')->nullable()->after('seo_h1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['seo_title', 'seo_description', 'seo_h1', 'seo_intro_text']);
        });
    }
};
