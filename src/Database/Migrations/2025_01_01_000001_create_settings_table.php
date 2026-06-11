<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('module', 100);
            $table->string('key', 150);
            $table->enum('type', ['string', 'integer', 'float', 'boolean', 'json', 'array', 'encrypted'])->default('string');
            $table->text('value')->nullable();
            $table->string('description', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_locked')->default(false);
            $table->timestamps();

            $table->unique(['module', 'key'], 'uq_settings_module_key');
            $table->index('module', 'idx_settings_module');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
