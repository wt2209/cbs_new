<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    public $table = 'room_type';
    public function rooms()
    {
        return $this->hasMany('App\Model\Room', 'type_id','id');
    }

    public function records()
    {
        return $this->hasManyThrough('App\Model\Record', 'App\Model\Room', 'type_id');
    }
}
