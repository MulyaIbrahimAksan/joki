<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('antreans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->constrained('pasiens')->onDelete('cascade');
            $table->foreignId('poli_id')->constrained('polis')->onDelete('cascade');
            $table->foreignId('dokter_id')->nullable()->constrained('dokters')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('nomor_antrian', 10);
            $table->string('barcode_code')->unique();
            $table->string('barcode_image')->nullable();
            $table->enum('status', ['menunggu', 'dipanggil', 'dilayani', 'selesai', 'batal'])->default('menunggu');
            $table->timestamp('dipanggil_at')->nullable();
            $table->timestamp('selesai_at')->nullable();
           $table->date('tanggal')->default(DB::raw('CURRENT_DATE'));
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('antreans');
    }
};
