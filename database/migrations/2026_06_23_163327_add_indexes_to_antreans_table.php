<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('antreans', function (Blueprint $table) {
            $table->index(['tanggal', 'status']);
            $table->index(['poli_id', 'tanggal', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('antreans', function (Blueprint $table) {
            $table->dropIndex(['tanggal', 'status']);
            $table->dropIndex(['poli_id', 'tanggal', 'status']);
        });
    }
};
