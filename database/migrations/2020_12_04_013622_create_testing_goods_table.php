<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestingGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('testing_goods', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->index();
            $table->float('price', 6, 2, true);
            $table->unsignedInteger('num');
            $table->unsignedInteger('version')->default(0);
        });

        Schema::create('testing_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('goods_id')->index();
            $table->unsignedInteger('goods_num');
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
        Schema::drop('testing_goods');
        Schema::drop('testing_orders');
    }
}