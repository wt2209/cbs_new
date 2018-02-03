<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    public $table = 'room_type';
    public function rooms()
    {
        $this->hasMany('App\Model\Room', 'type_id','id');
    }
}
