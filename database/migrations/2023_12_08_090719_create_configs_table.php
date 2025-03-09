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
        Schema::create('configs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('appname');
            $table->string('subname');
            $table->string('skin');
            $table->string('copyright');
            $table->string('version');
            $table->string('poster');
            $table->string('logo');
            $table->string('bg');
            $table->smallInteger('sts_use_tbp')->nullable()->default(1)->comment('0: STS tidak memperhatikan TBP; 1 STS memperhatikan TBP');
            $table->smallInteger('uva')->nullable()->default(1)->comment('0: tidak menggunakan virtual account; 1 menggunakan virtual account');
            $table->string('url_sync_bjtm', 20)->nullable();
            $table->string('url_proxy_sync_bjtm', 20)->nullable()->comment('digunakan untuk proxy app terhadap url_sync_bjtm karena saat ini hanya satu ip yang terdaftar di BJTM');
            $table->integer('port_proxy_sync_bjtm')->nullable()->default(0)->comment('digunakan untuk port proxy app terhadap url_sync_bjtm karena saat ini hanya satu ip yang terdaftar di BJTM');
            $table->string('user_auth', 50)->nullable()->comment('User Auth untuk synchronize terhadap BJTM');
            $table->string('pass_auth', 50)->nullable()->comment('Password Auth untuk synchronize terhadap BJTM');
            $table->string('url_sync_va_bjtm', 100)->nullable()->comment('digunakan sebagai url untuk create va, baik production maupun dev');
            $table->string('identity_bjtm_va', 10)->nullable()->comment('digunakan sebagai identity oleh BJTM');
            $table->string('identity_kasda_va', 10)->nullable()->comment('digunakan sebagai identity oleh Kasda');
            $table->string('url_sync_api_ecounting', 100)->nullable()->comment('digunakan sebagai url untuk synchronisasi dengan ecounting');
            $table->smallInteger('tahapan_id')->nullable()->default(0)->comment('digunakan untuk menentukan transaksi TBP, STS, BKU menggunakan tahapan anggaran terakhir');
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
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
        Schema::dropIfExists('configs');
    }
};
