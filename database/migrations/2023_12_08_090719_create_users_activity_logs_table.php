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
        Schema::create('users_activity_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable()->index('user_activity_logs_user_id_foreign');
            $table->unsignedInteger('admin_id')->nullable()->index('user_activity_logs_admin_id_foreign');
            $table->string('activity')->nullable();
            $table->string('table')->nullable();
            $table->string('subject')->nullable();
            $table->text('desc')->nullable();
            $table->text('data')->nullable();
            $table->string('url')->nullable();
            $table->string('method')->nullable();
            $table->string('ip')->nullable();
            $table->string('agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_activity_logs');
    }
};
