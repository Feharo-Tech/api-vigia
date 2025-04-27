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
        Schema::create('apis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('url');
            $table->string('method')->default('GET');
            $table->text('expected_response')->nullable();
            $table->integer('expected_status_code')->default(200);
            $table->integer('check_interval')->default(5);
            $table->boolean('is_active')->default(true);
            $table->json('headers')->nullable();
            $table->json('body')->nullable();
            $table->timestamp('last_checked_at')->nullable()->after('is_active');
            $table->integer('error_threshold')->default(5)->after('check_interval');
            $table->integer('timeout_threshold')->default(30)->after('error_threshold');
            $table->boolean('should_notify')->default(true)->after('timeout_threshold');
            $table->timestamps();

            $table->index(['is_active', 'last_checked_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apis');
    }
};
