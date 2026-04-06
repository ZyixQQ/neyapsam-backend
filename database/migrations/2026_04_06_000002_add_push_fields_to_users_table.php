<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('push_token')->nullable()->after('is_admin');
            $table->string('platform', 10)->nullable()->after('push_token');   // ios | android
            $table->boolean('push_notifications_enabled')->default(false)->after('platform');
            $table->timestamp('push_token_updated_at')->nullable()->after('push_notifications_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['push_token', 'platform', 'push_notifications_enabled', 'push_token_updated_at']);
        });
    }
};
