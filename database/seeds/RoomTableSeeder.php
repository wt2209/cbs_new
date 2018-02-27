<?php

use Illuminate\Database\Seeder;

class RoomTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (config('app.debug')) {
            for ($i = 1; $i <= 4; $i++) {
                $top = $i < 3 ? 18 : 16;
                for ($j = 1; $j <= $top; $j++) {
                    for ($m = 1; $m <= 17; $m++) {
                        DB::table('room')->insert([
                            'company_id'=> 0,
                            'building'=>$i,
                            'type_id'=> 1,
                            'room_name'=> $i*10000+$j*100+$m,
                            'person_number'=>8,
                            'price'=>768,
                            'room_remark'=> '房间备注'.str_random(10),
                            'created_at'=>date('Y-m-d H:i:s'),
                            'updated_at'=>date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }
            for ($i = 1; $i <= 9; $i++) {
                DB::table('room')->insert([
                    'company_id'=> 0,
                    'building'=>'综合楼',
                    'type_id'=> 2,
                    'room_name'=> '餐厅'.$i,
                    'price'=>0,
                    'room_remark'=> '餐厅备注'.str_random(10),
                    'created_at'=>date('Y-m-d H:i:s'),
                    'updated_at'=>date('Y-m-d H:i:s'),
                ]);
            }
            for ($i = 1; $i <= 5; $i++) {
                DB::table('room')->insert([
                    'company_id'=> 0,
                    'building'=>'综合楼',
                    'type_id'=> 3,
                    'room_name'=> '办公'.$i,
                    'price'=>0,
                    'room_remark'=> '办公备注'.str_random(10),
                    'created_at'=>date('Y-m-d H:i:s'),
                    'updated_at'=>date('Y-m-d H:i:s'),
                ]);
            }

        } else {
            for ($i = 1; $i <= 4; $i++) {
                $top = $i < 3 ? 18 : 16;
                for ($j = 1; $j <= $top; $j++) {
                    for ($m = 1; $m <= 17; $m++) {
                        DB::table('room')->insert([
                            'company_id'=> 0,
                            'building'=>$i,
                            'type_id'=> 1,
                            'room_name'=> $i*10000+$j*100+$m,
                            'person_number'=>8,
                            'price'=>768,
                            'room_remark'=> '',
                            'created_at'=>date('Y-m-d H:i:s'),
                            'updated_at'=>date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }
        }

    }
}
