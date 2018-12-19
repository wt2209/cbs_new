<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBelongToColumnToRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('records', function ($table) {
            $table->string('belongs_to')->default('')->comment('值：“修船” 或 “造船” ');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('records', 'belongs_to')) {
            Schema::table('records', function ($table) {
                $table->dropColumn('belongs_to');
            });
        }
    }
}
