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
        Schema::create('school_time_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name'); // Nama jadwal, misalnya "Reguler", "Ujian", dll
            $table->enum('day', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']);
            $table->time('entry_time'); // Jam masuk yang diharapkan
            $table->time('exit_time'); // Jam pulang yang diharapkan
            $table->time('late_threshold')->nullable(); // Batas waktu dianggap terlambat
            $table->string('academic_year'); // Tahun akademik
            $table->string('semester')->nullable(); // Semester
            $table->boolean('is_active')->default(true); // Status aktif
            $table->text('description')->nullable(); // Deskripsi jadwal
            $table->timestamps();
            
            // Memastikan tidak ada duplikasi jadwal untuk hari yang sama
            $table->unique(['day', 'academic_year', 'semester']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_time_settings');
    }
};
