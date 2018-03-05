<?php

namespace App\Model;

use App\Model\Company;
use Illuminate\Database\Eloquent\Model;

class UtilityBase extends Model
{
    //表名
    protected $table='utility_base';

    //主键
    protected $primaryKey = 'u_base_id';

    public function room()
    {
        return $this->belongsTo('App\Model\Room', 'room_id', 'room_id');
    }
}
