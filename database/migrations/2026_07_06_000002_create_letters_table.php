<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('letters', function (Blueprint $table) {
            $table->id();
            $table->string('letter_number', 50)->unique();
            $table->enum('letter_type', ['masuk', 'keluar']);
            $table->date('letter_date');
            $table->string('sender_name');
            $table->string('sender_email')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('category', 50);
            $table->string('subject', 100);
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('original_file_name');
            $table->string('file_mime')->nullable();
            $table->unsignedInteger('file_size')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letters');
    }
};
