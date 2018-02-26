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
        if ($value == '3000-01-01 00:00:00') {
            return '';
        }
        return substr($value,0,10);
    }


    /**
     * 添加一条记录，
     * 即某个公司的某个房间办理入住
     */
    public function scopeAddOneRecord($query, $roomID, $companyId, array $utilities, $gender = 1, $remark = '', $enteredAt = null)
    {
        if (!$enteredAt) {
            $enteredAt = date('Y-m-d H:i:s');
        }

        $data = [
            'company_id' => $companyId,
            'room_id' => $roomID,
            'entered_at' => $enteredAt,
            'enter_electric_base' => $utilities['enter_electric_base'],
            'enter_water_base' => $utilities['enter_water_base'],
            'remark' => $remark,
            'gender' => $gender,
        ];

        $query->create($data);
    }

    /**
     * 完成一条记录，
     * 即某个公司的某个房间办理退房
     */
    public function scopeCompleteOneRecord($query, $roomID, $companyId, array $utilities, $remark = '', $quitAt = null)
    {
        if (!$quitAt) {
            $quitAt = date('Y-m-d H:i:s');
        }

        $data = [
            'quit_at' => $quitAt,
            'quit_electric_base' => $utilities['quit_electric_base'],
            'quit_water_base' => $utilities['quit_water_base'],
        ];

        if ($remark) {
            $data['remark'] = $remark;
        }

        $query->where('company_id', $companyId)
            ->where('room_id', $roomID)
            ->update($data);
    }
}
