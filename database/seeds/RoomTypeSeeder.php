<?php

use Illuminate\Database\Seeder;

class RoomTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('room_type')->insert([
            'type_name'=> '居住用房'
        ]);
        DB::table('room_type')->insert([
            'type_name'=> '餐厅'
        ]);
        DB::table('room_type')->insert([
            'type_name'=> '服务用房'
        ]);
    }
}
