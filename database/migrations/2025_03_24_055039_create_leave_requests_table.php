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
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('student_id');
            $table->uuid('approved_by')->nullable(); // Admin/Guru yang menyetujui/menginput
            $table->enum('request_type', ['Sakit', 'Izin', 'Pulang Awal']);
            $table->date('start_date');
            $table->date('end_date');
            $table->time('early_leave_time')->nullable(); // Waktu pulang yang diminta
            $table->text('reason');
            $table->string('attachment')->nullable();
            
            // Status hanya untuk izin dan sakit, tidak untuk pulang awal
            $table->enum('status', ['Menunggu', 'Disetujui', 'Ditolak'])
                ->nullable(); // Null untuk pulang awal yang langsung disetujui
                
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('student_id')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
