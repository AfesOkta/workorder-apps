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
        Schema::create('users_logins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index('users_logins_user_id_foreign');
            $table->string('user_ip', 50)->nullable();
            $table->string('location', 91)->nullable();
            $table->text('browser')->nullable();
            $table->string('os', 50)->nullable();
            $table->string('longitude', 25)->nullable();
            $table->string('latitude', 25)->nullable();
            $table->string('country', 30)->nullable();
            $table->string('country_code', 15)->nullable();
            $table->timestamps();
            $table->tinyInteger('user_skpd')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_logins');
    }
};
