<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    public function getEnteredAtAttribute($value)
    {
        return substr($value,0,10);
    }

    public function getQuitAtAttribute($value)
    {
        if ($value == '3000-01-01 00:00:00') {
            return '';
        }
        return substr($value,0,10);
    }
}
