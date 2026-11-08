<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->enum('kategori', ['raport', 'ijazah', 'sertifikat']);
            $table->enum('jenis_sertifikat', ['TOEIC', 'TKA', 'UKK', 'PKL'])->nullable();
            $table->tinyInteger('semester')->nullable();
            $table->string('nama_file');
            $table->string('file_path');
            $table->foreignId('uploaded_by')->constrained('gurus');
            $table->timestamps();

            $table->index('kategori');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumens');
    }
};
