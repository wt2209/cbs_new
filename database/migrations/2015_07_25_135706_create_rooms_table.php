<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room', function (Blueprint $table) {
            $table->increments('room_id');
            $table->string('building');
            $table->string('room_name', 20);//房间名称
            $table->integer('company_id', false, true)->default(0);
            //TODO  重新确定房间类型种类
            //房间类型：1|住房， 2|餐厅， 3|服务用房 ......
            $table->tinyInteger('type_id', false, true)->default(1);
            //房间人数  房间人数决定了收费标准
            $table->tinyInteger('person_number')->default(0)->comment('房间人数');
            $table->decimal('price')->comment('每月房费单价');
            $table->string('room_remark');
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
        Schema::drop('room');
    }
}
