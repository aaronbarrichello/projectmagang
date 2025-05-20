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
        Schema::create('visit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('requests')->onDelete('cascade');
            $table->string('type'); // check_in, check_out
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit_logs');
    }
};