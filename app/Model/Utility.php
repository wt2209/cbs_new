<?php

namespace App\Model;

use App\Model\Company;
use Illuminate\Database\Eloquent\Model;

class Utility extends Model
{
    //表名
    protected $table='utility';

    //主键
    protected $primaryKey = 'utility_id';

    public function room()
    {
        return $this->belongsTo('App\Model\Room', 'room_id', 'room_id');
    }
}
