<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    protected $guarded = ['id'];

    public function company()
    {
        return $this->belongsTo('App\Model\Company', 'company_id', 'company_id');
    }

    public function room()
    {
        return $this->belongsTo('App\Model\Room', 'room_id', 'room_id');
    }

    
    public function getEnteredAtAttribute($value)
    {
        return substr($value,0,10);
    }

    public function getQuitAtAttribute($value)
    {
        if ($value == '0000-00-00 00:00:00') {
            return '';
        }
        return substr($value,0,10);
    }

    public function getQuitElectricBaseAttribute($value)
    {
        if ($value == 0) {
            return '';
        }
        return $value;
    }

    public function getQuitWaterBaseAttribute($value)
    {
        if ($value == 0) {
            return '';
        }
        return $value;
    }
}
