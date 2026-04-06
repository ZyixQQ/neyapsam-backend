<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();

            // Bakım Modu
            $table->boolean('maintenance_mode')->default(false);
            $table->text('maintenance_message')->nullable();

            // Güncelleme / Gateway
            $table->boolean('force_update_required')->default(false);
            $table->string('minimum_version', 20)->nullable();
            $table->string('latest_version', 20)->nullable();
            $table->text('update_message')->nullable();
            $table->string('update_ios_url', 500)->nullable();
            $table->string('update_android_url', 500)->nullable();

            // Duyuru
            $table->boolean('show_announcement')->default(false);
            $table->string('announcement_title', 255)->nullable();
            $table->text('announcement_message')->nullable();
            $table->string('announcement_type', 50)->default('info'); // info | warning | success

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
