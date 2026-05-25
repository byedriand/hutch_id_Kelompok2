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
        Schema::create('notifikasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan')->onDelete('cascade');
            $table->enum('tipe', ['stok_kurang', 'stok_ditambah', 'pesanan_dibuat', 'pesanan_dikonfirmasi']);
            $table->string('judul');
            $table->text('pesan');
            $table->json('data')->nullable();
            $table->json('untuk_roles')->nullable()->comment('JSON array of roles to notify');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('dibaca_at')->nullable();
            $table->timestamps();
            $table->index('pesanan_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifikasis');
    }
};
