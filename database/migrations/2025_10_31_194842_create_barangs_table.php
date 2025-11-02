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
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->maxLength(100);
            $table->foreignId('jenis_id')->constrained('jenis')->onDelete('restrict');
            $table->foreignId('merek_id')->constrained('mereks')->onDelete('restrict');
            $table->string('sn')->unique()->maxLength(100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
