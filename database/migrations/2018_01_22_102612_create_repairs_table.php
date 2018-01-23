<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRepairsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repairs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('input_man')->default('')->comment('录入人');
            $table->string('name')->default('')->comment('报修人');
            $table->string('location')->default('')->comment('位置');
            $table->string('content')->default('');
            $table->string('phone_number')->default('');
            $table->timestamp('report_at')->comment('报修时间');
            $table->tinyInteger('is_reviewed')->default(0)->comment('是否审核');
            $table->string('reviewer')->default('')->comment('审核人');
            $table->tinyInteger('is_passed')->default(0)->comment('是否通过审核');
            $table->string('review_remark')->default('')->comment('审核意见');
            $table->timestamp('reviewed_at')->comment('审核时间');
            $table->tinyInteger('is_printed')->default(0)->comment('是否打印');
            $table->timestamp('printed_at')->comment('打印时间');
            $table->tinyInteger('is_finished')->default(0)->comment('是否完工');
            $table->timestamp('finished_at')->comment('完工时间');
            $table->string('finish_remark')->default('')->comment('完工说明');
            $table->string('comment')->default('')->comment('完工评价');
            $table->tinyInteger('canceled')->default(0)->comment('项目取消');
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
        Schema::drop('repairs');
    }
}
