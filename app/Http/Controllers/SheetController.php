<?php

namespace App\Http\Controllers;

use App\Model\CompanyLog;
use PHPExcel_Worksheet;
use App\Model\Room;
use App\Model\Company;
use App\Model\UtilityBase;
use App\Model\Record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ExcelController;

class SheetController extends Controller
{

    public function __construct()
    {
        $this->middleware('my.auth');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex()
    {
        return view('sheet.index');
    }

    public function getCreate(Request $request)
    {
         //字段验证
         $validator = Validator::make($request->all(), [
            'year'=>'required|integer|min:2017',
            'month' => 'required|integer|between:1,12',
        ]);
        //验证不通过，返回第一个错误信息
        if ($validator->fails()) {
            return exit($validator->errors()->first(). '<a href="'.url('sheet/index').'">返回</a>');
        }
    
        $year = $request->year;
        $month = $request->month;

        $fileName = $year.'.'.$month.'月报表 - '.date('Ymd');

        $lastMonth = $month == 1 ? 12 : $month - 1;
        $lastYear = $month == 1 ? $year - 1 : $year;

        $firstDay = 1;
        $lastDay = date('t', strtotime($year . '-' . $month));

        $nextMonthFirstDay = date('Y-m-d H:i:s', strtotime('next month', strtotime($year . '-' . $month)));
        $lastMonthLastDay = date('Y-m-t 23:59:59', strtotime('last month', strtotime($year . '-' . $month)));

        // echo " 当前：{$year}-{$month}";
        // echo " 上：{$preYear}-{$preMonth}";
        // echo " 最后一天：{$lastDay}";
        // echo " shang月zuihou天：{$lastMonthLastDay }";
        // die;
       
        //上月水电底数
        $lastMonthBases = $this->getBases($lastYear, $lastMonth);
        $currentMonthBases = $this->getBases($year, $month);
        
        $records = Record::where('entered_at', '<', $nextMonthFirstDay)
            ->where(function($query) use($lastMonthLastDay){
                $query->where('quit_at', '0000-00-00 00:00:00')
                    ->orWhere('quit_at', '>', $lastMonthLastDay);
            })
            ->get();

        
        $data = [];

        foreach ($records as $record) {
            $roomId = $record->room_id;
            $tmp = [
                'company_name' => $record->company->company_name,
                'belongs_to' => $record->company->belongs_to,
                'room_name' => $record->room->room_name,
                'price' => $record->price,
            ];

            if (strtotime($record->entered_at) <= strtotime($lastMonthLastDay)) {
                $tmp['start_day'] = $firstDay;
                $tmp['start_electric_base'] = isset($lastMonthBases[$roomId]['electric_base']) 
                                                ? $lastMonthBases[$roomId]['electric_base'] 
                                                : 0;
                $tmp['start_water_base'] = isset($lastMonthBases[$roomId]['water_base']) 
                                                ? $lastMonthBases[$roomId]['water_base'] 
                                                : 0;
            } else {
                $tmp['start_day'] = date('d', strtotime($record->entered_at));
                $tmp['start_electric_base'] = $record->enter_electric_base;
                $tmp['start_water_base'] = $record->enter_water_base;
            }

            if ($record->in_use == 1 || strtotime($record->quit_at) >= strtotime($nextMonthFirstDay)) { // 还没退房
                $tmp['end_day'] = $lastDay;
                $tmp['end_electric_base'] = isset($currentMonthBases[$roomId]['electric_base']) 
                                            ? $currentMonthBases[$roomId]['electric_base'] 
                                            : 0;
                $tmp['end_water_base'] = isset($currentMonthBases[$roomId]['water_base']) 
                                            ? $currentMonthBases[$roomId]['water_base'] 
                                            : 0;
            } else {
                $tmp['end_day'] = date('d', strtotime($record->quit_at));
                $tmp['end_electric_base'] = $record->quit_electric_base;
                $tmp['end_water_base'] = $record->quit_water_base;
            }

            $tmp['day_number']  = $tmp['end_day'] - $tmp['start_day'] + 1;
            
            $tmp['rent_money'] = round($tmp['price'] * $tmp['day_number'] / $lastDay, 2);

            $tmp['electric'] = $tmp['end_electric_base'] - $tmp['start_electric_base'];
            $tmp['water'] = $tmp['end_water_base'] - $tmp['start_water_base'];

            $tmp['electric_money'] = round($tmp['electric'] * config('cbs.electricMoney'), 2);
            $tmp['water_money'] = round($tmp['water'] * config('cbs.waterMoney'), 2);

            $tmp['total_money'] = $tmp['rent_money'] + $tmp['electric_money'] + $tmp['water_money'];

            //错误处理
            if ($tmp['electric'] < 0) {
                $tmp['electric'] = '错误';
                $tmp['electric_money'] = '错误';
                $tmp['total_money'] = '错误';
            }
            if ($tmp['water'] < 0) {
                $tmp['water'] = '错误';
                $tmp['water_money'] = '错误';
                $tmp['total_money'] = '错误';
            }

            $data[] = $tmp;
        }

        $this->exportReport($year, $month, $data);
    }

    private function getBases($year, $month)
    {
        $bases = UtilityBase::where('year', $year)
            ->where('month', $month)
            ->get();

        $ret = [];
        foreach ($bases as $base) {
            $ret[$base->room_id]['electric_base'] = $base->electric_base;
            $ret[$base->room_id]['water_base'] = $base->water_base;
        }
        return $ret;
    }


    private function exportReport($year, $month, $records)
    {
        $filename = $year . '年' . $month . '月报表源数据' . '-' . date('Ymd');
        //标题行 
        $titleRow = [$filename];
        //菜单第一行
        $menuRow = ['序号', '公司名','属于', '房间号', '开始日', '结束日', '天数', '月租金', '房费',
                    '上期电表数', '本期电表数', '用电量', '电费', 
                    '上期水表数', '本期水表数', '用水量', '水费', 
                    '总金额'];
        $data = [
            $titleRow,
            $menuRow,
        ];
        // 序号
        $serialNumber = 1;
        foreach ($records as $record) {
            $tmp = [
                $serialNumber++,
                $record['company_name'],
                $record['belongs_to'],
                $record['room_name'],
                $record['start_day'],
                $record['end_day'],
                $record['day_number'],
                ($record['price'] == '0.00' || $record['price'] == '0') ? '' : $record['price'],
                $record['rent_money'] ?: '',
                $record['start_electric_base'],
                $record['end_electric_base'],
                $record['electric'],
                $record['electric_money'],
                $record['start_water_base'],
                $record['end_water_base'],
                $record['water'],
                $record['water_money'],
                $record['total_money'],
            ];
            $data[] = $tmp;
        }
        ExcelController::exportFile($filename, $data);
    }

}
