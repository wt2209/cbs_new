<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    protected $guarded = ['id'];

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
}
