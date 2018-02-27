<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'room_id'=>[
            'required' => '失败：必选选择或填写房间',
            'integer'=>'失败：房间ID必须是一个整数！',
            'min'=>'失败：房间ID格式错误！'
        ],
        'building' => [
            'required' => '失败：必须填写楼号！',
        ],
        'room_number' => [
            'required' => '失败：必须填写房间号！',
            'integer'=>'失败：房间号必须是一个数字！',
            'max'=>'失败：房间号必须小于65535',
            'min'=>'失败：房间号必须大于1'
        ],
        'company_id' => [
            'required' => '失败：必选选择或填写公司名',
            'integer'=>'失败：公司ID必须是一个整数！',
            'min'=>'失败：公司ID格式错误！'
        ],
        'company_name'=>[
            'required'=>'失败：必须填写公司名称！',
            'between'=>'失败：公司名称不得多于255个字符！'
        ],
        'company_description'=>[
            'between'=>'失败：公司描述不得多于255个字符！'
        ],
        'linkman'=>[
            'required'=>'失败：必须填写日常联系人姓名！',
            'between'=>'失败：请填写正确的日常联系人姓名！'
        ],
        'linkman_tel'=>[
            'numeric'=>'失败：请填写正确的联系人电话！'
        ],
        'manager'=>[
            'between'=>'失败：请填写正确的负责人姓名！'
        ],
        'manager_tel'=>[
            'numeric'=>'失败：请填写正确的负责人电话！'
        ],
        'company_remark'=>[
            'between'=>'失败：备注不得多于255个字符！'
        ],

        //处罚相关
        'money'=>[
            'required'=>'失败：金额必须填写！',
            'numeric'=>'失败：请填写正确的数额！'
        ],
        'reason'=>[
            'required'=>'失败：原因必须填写！',
            'between'=>'失败：原因不得多于255个字符！'
        ],
        'punish_remark'=>[
            'between'=>'失败：备注不得多于255个字符！'
        ],

        //水电表底数
        'enter_electric_base' =>[
            'required' => '失败：入住电表底数必须填写！',
            'numeric' => "失败：入住电表底数必须是数字！",
        ],
        'enter_water_base' =>[
            'required' => '失败：入住水表底数必须填写！',
            'numeric' => "失败：入住水表底数必须是数字！",
        ],
        'quit_electric_base' =>[
            'required' => '失败：退房电表底数必须填写！',
            'numeric' => "失败：退房电表底数必须是数字！",
        ],
        'quit_water_base' =>[
            'required' => '失败：退房水表底数必须填写！',
            'numeric' => "失败：退房水表底数必须是数字！",
        ],

        //时间
        'quit_at' => [
            'date_format' => '失败：日期格式错误！',
        ],
        'entered_at' => [
            'date_format' => '失败：日期格式错误！',
        ],

        //性别
        'gender' => [
            'required'=>"失败：必须选取性别！",
            'between'=>'失败：性别取值错误！',
        ],
    ],


];
