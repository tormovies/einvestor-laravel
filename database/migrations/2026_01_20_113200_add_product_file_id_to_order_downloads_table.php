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
        Schema::table('order_downloads', function (Blueprint $table) {
            $table->foreignId('product_file_id')->nullable()->after('order_item_id')
                ->constrained('product_files')->onDelete('cascade');
            
            $table->index('product_file_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_downloads', function (Blueprint $table) {
            $table->dropForeign(['product_file_id']);
            $table->dropColumn('product_file_id');
        });
    }
};
