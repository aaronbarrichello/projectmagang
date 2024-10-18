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
        Schema::create('requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('uuid', 36);
            $table->string('visit_purpose');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->text('description');
            $table->string('spk')->nullable();
            $table->string('qrcode')->nullable();
            $table->enum('status', ['requested', 'accepted', 'rejected', 'finished', 'missed'])->default('requested');
            $table->timestamps();
        });

        Schema::create('visitors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('uuid', 36);
            $table->string('ktp');
            $table->string('name');
            $table->string('file_ktp');
            $table->string('file_nda')->nullable();
            $table->string('company');
            $table->string('occupation');
            $table->string('phone');
            $table->string('email');
            $table->boolean('pic');
            $table->timestamps();
        });

        Schema::create('request_visitor', function (Blueprint $table) {
            $table->unsignedBigInteger('request_id');
            $table->unsignedBigInteger('visitor_id');

            $table->foreign('request_id')
                ->references('id')
                ->on('requests')
                ->onDelete('cascade');

            $table->foreign('visitor_id')
                ->references('id')
                ->on('visitors')
                ->onDelete('cascade');

            $table->primary(['request_id', 'visitor_id'], 'request_visitor_visitor_id_request_id_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
        Schema::dropIfExists('visitors');
        Schema::dropIfExists('request_visitor');
    }
};
