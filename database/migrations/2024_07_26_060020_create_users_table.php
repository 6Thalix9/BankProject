<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('bank_id');
            $table->string('email')->unique();
            $table->string('password'); 
            $table->rememberToken(); 
            $table->timestamps();

            $table->foreign('bank_id')->references('id')->on('banks')->onDelete('cascade');
        });

        Schema::create('password_resets', function (Blueprint $table) { 
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('ip_address', 45);
            $table->text('user_agent');
            $table->text('payload');
            $table->integer('last_activity');
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_resets'); 
        Schema::dropIfExists('users');
    }
};