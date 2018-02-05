<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->integer('room_id');
            $table->timestamp('entered_at')->comment('入住时间');
            $table->timestamp('quit_at')->default('3000-01-01 00:00:00')->comment('退房时间');
            $table->integer('enter_electric_base')->default(0)->comment('入住时电表底数');
            $table->integer('enter_water_base')->default(0)->comment('入住时水表底数');
            $table->integer('quit_electric_base')->default(0)->comment('退房时电表底数');
            $table->integer('quit_water_base')->default(0)->comment('退房时水表底数');
            $table->string('remark');
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
        Schema::drop('records');
    }
}
