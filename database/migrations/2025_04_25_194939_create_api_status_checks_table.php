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
        Schema::create('api_status_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_id')->constrained()->onDelete('cascade');
            $table->integer('status_code');
            $table->float('response_time');
            $table->boolean('success');
            $table->text('response_body')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_status_checks');
    }
};
