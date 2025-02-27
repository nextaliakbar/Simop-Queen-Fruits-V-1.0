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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('f_name', 100)->nullable();
            $table->string('l_name', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->unique();
            $table->string('image', 100)->nullable();
            $table->string('password', 100);
            $table->string('remember_token', 100)->nullable();
            $table->string('fcm_token', 255)->nullable();
            $table->bigInteger('admin_role_id')->unsigned();
            $table->boolean('status')->default(true);
            $table->string('identity_number', 30)->nullable();
            $table->string('identity_type', 30)->nullable();
            $table->string('identity_image', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
