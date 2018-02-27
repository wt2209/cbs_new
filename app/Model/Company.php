<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    //表名
    protected $table='company';

    //主键
    protected $primaryKey = 'company_id';

    //公司与房间是一对多的关系
    public function Rooms()
    {
        return $this->hasMany('App\Model\Room', 'company_id', 'company_id');
    }

    public function getCreatedAtAttribute($value)
    {
        return substr($value, 0, 10);
    }

    public function getQuitAtAttribute($value)
    {
        if ($value == '0000-00-00 00:00:00') {
            return '';
        }
        return substr($value, 0, 10);
    }
}
