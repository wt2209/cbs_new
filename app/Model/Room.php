<?php

namespace App\Model;

use App\Model\Company;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    //表名
    protected $table='room';

    //主键
    protected $primaryKey = 'room_id';
    //允许读写的字段
    protected $fillable = ['building', 'room_number', 'floor', 'room_remark', 'room_id'];


    //房间与承包商公司一对多关系
    public function company()
    {
        return $this->belongsTo('App\Model\Company', 'company_id', 'company_id');
    }

    /**
     * room-type
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo('App\Model\RoomType', 'type_id', 'id');
    }

    public function records()
    {
        return $this->hasMany('App\Model\Record');
    }
}
