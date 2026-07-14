<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('data_id')->nullable()->index();
            $table->string('data_module', 100)->nullable()->index();
            $table->string('data_code', 100)->nullable()->index();
            $table->string('data_name')->nullable()->index();
            $table->string('data_type')->nullable()->index();
            $table->timestamp('data_date')->nullable()->index();
            $table->string('data_status')->nullable()->index();
            $table->string('action', 50);
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->json('log_data')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings_logs');
    }
};
