<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('username')->nullable()->unique('users_username');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->string('nip')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('alamat')->nullable();
            $table->string('npwp')->nullable();
            $table->string('ttd')->nullable();
            $table->string('nama_bank')->nullable();
            $table->text('no_rekening')->nullable();
            $table->timestamps();
            $table->tinyInteger('status')->nullable()->default(0)->comment('0: User|1: Admin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
