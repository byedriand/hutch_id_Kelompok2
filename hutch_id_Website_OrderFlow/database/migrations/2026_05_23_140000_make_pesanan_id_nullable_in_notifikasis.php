<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Drop the existing foreign key
        Schema::table('notifikasis', function (Blueprint $table) {
            $table->dropForeign(['pesanan_id']);
        });
        
        // Make pesanan_id nullable
        DB::statement('ALTER TABLE notifikasis MODIFY pesanan_id BIGINT UNSIGNED NULL;');
        
        // Re-add the foreign key
        Schema::table('notifikasis', function (Blueprint $table) {
            $table->foreign('pesanan_id')
                ->references('id')
                ->on('pesanan')
                ->onDelete('cascade');
        });
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        Schema::table('notifikasis', function (Blueprint $table) {
            $table->dropForeign(['pesanan_id']);
        });
        
        DB::statement('ALTER TABLE notifikasis MODIFY pesanan_id BIGINT UNSIGNED NOT NULL;');
        
        Schema::table('notifikasis', function (Blueprint $table) {
            $table->foreign('pesanan_id')
                ->references('id')
                ->on('pesanan')
                ->onDelete('cascade');
        });
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
