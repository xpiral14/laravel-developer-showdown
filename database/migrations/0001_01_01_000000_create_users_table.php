<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unsynchronized_users');
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email')->unique();
            $table->string('time_zone');
            $table->timestamps();

            $table->index('email');
        });

        Schema::create('unsynchronized_users', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->references('id')->on('users');
            $table->json('data')->nullable();
            $table->timestamps();
        });

        //
        //Schema::create('password_reset_tokens', function (Blueprint $table) {
        //    $table->string('email')->primary();
        //    $table->string('token');
        //    $table->timestamp('created_at')->nullable();
        //});

        //Schema::create('sessions', function (Blueprint $table) {
        //    $table->string('id')->primary();
        //    $table->foreignId('user_id')->nullable()->index();
        //    $table->string('ip_address', 45)->nullable();
        //    $table->text('user_agent')->nullable();
        //    $table->longText('payload');
        //    $table->integer('last_activity')->index();
        //});
    }
};
