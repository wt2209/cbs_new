<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Record;
use App\Model\Room;
use App\Model\Company;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class RecordController extends Controller
{

    /**
     * 错误提示信息
     */
    public $errorMsg = '失败：未知错误！';


    /**
     * 房间使用记录
     */
    public function getIndex()
    {
        $records = Record::with('company')->with('room')->paginate(config('cbs.pageNumber'));;

        $companies = Company::get();

        return view('record.index', compact('records', 'companies'));
    }

    public function getSearch(Request $request)
    {
        $companyId = $request->company_id;
        $inUse = $request->in_use;

        if ($companyId == 0) {
            $model = Record::where('company_id', '>', 0);
        } else {
            $model = Record::where('company_id', $companyId);
        }
        
        if ($inUse == 1 || $inUse == 0) {
            $model->where('in_use', $inUse);
        }

        $records = $model->paginate(config('cbs.pageNumber'));;

        $companies = Company::get();

        return view('record.index', compact('records', 'companies'));
    }

    /**
     * 批量入住
     */
    public function postMassCreate(Request $request)
    {
        $companyId = intval($request->company_id);
        $items = explode('|', $request->rooms);

        foreach ($items as $item) {
            $tmp = explode('_', $item);
            $roomId = (int) $tmp[0];
            $gender = (int) $tmp[1];
            $enterElectricBase = (int) $tmp[2];
            $enterWaterBase = (int) $tmp[3];
            $data = [
                'room_id' => $roomId,
                'company_id' => $companyId,
                'gender' => $gender,
                'enter_electric_base' => $enterElectricBase,
                'enter_water_base' => $enterWaterBase,
                'entered_at' => date('Y-m-d H:i:s'),
            ];

            $this->addOneRecord($data);
        }
        return response()->json(['message'=>'操作成功！', 'status'=>1]);
    }

    /**
     * 某个公司入住某个房间
     */
    public function postCreate(Request $request)
    {
        //字段验证
        $validator = Validator::make($request->all(), [
            'company_id'=>'required|integer|min:1',
            'room_id' => 'required|integer|min:1',
            'enter_electric_base' => 'required|numeric',
            'enter_water_base' => 'required|numeric',
            'gender' => 'required|between:1,2',
            'entered_at'=> 'date_format:Y-m-d',
        ]);
        //验证不通过，返回第一个错误信息
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->errors()->first(), 'status'=>0]);
        }
    
        $data = [
            'room_id' => $request->room_id,
            'company_id' => $request->company_id,
            'gender' => $request->gender,
            'enter_electric_base' => $request->enter_electric_base,
            'enter_water_base' => $request->enter_water_base,
            'entered_at' => $request->entered_at ? $request->entered_at : date('Y-m-d H:i:s'),
        ];
        if ($this->addOneRecord($data)){
            return response()->json(['message'=>'操作成功！', 'status'=>1]);
        }

        return response()->json(['message'=>$this->errorMsg, 'status'=>0]);
    }

    public function postComplete(Request $request) 
    {
        //字段验证
        $validator = Validator::make($request->all(), [
            'company_id'=>'required|integer|min:1',
            'room_id' => 'required|integer|min:1',
            'quit_electric_base' => 'required|numeric',
            'quit_water_base' => 'required|numeric',
            'quit_at'=> 'date_format:Y-m-d',
        ]);
        //验证不通过，返回第一个错误信息
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->errors()->first(), 'status'=>0]);
        }

        $data = [
            'room_id' => $request->room_id,
            'company_id' => $request->company_id,
            'quit_electric_base' => $request->quit_electric_base,
            'quit_water_base' => $request->quit_water_base,
            'quit_at' => $request->quit_at ? $request->quit_at : date('Y-m-d H:i:s'),
        ];
        if ($this->completeOneRecord($data)){
            return response()->json(['message'=>'操作成功！', 'status'=>1]);
        }

        return response()->json(['message'=>$this->errorMsg, 'status'=>0]);
    }

    /**
     * 创建一条记录，
     * 即房间入住
     */
    private function addOneRecord($data)
    {
        if ($this->checkRoomIsNotEmpty($data['room_id'])) {
            $this->errorMsg = '失败：此房间已被占用！';
            return false;
        }
        
        $roomId = $data['room_id'];
        $companyId = $data['company_id'];

        $data['price'] = Room::where('room_id', $roomId)->value('price');
        $data['in_use'] = 1; //标记为正在使用
        if (Record::create($data)) {
            return Room::where('room_id', $roomId)->update(['company_id' => $companyId]);
        }
        return false;
    }

    /**
     * 完成一条记录，
     * 即房间退房
     */
    private function completeOneRecord($data)
    {
        if ($this->checkRoomIsNotEmpty($data['room_id'])) { //房间非空，可以完成记录
            $insert = [
                'in_use' => 0, //标记为已空
                'quit_at' => date('Y-m-d H:i:s'),
                'quit_electric_base' => $data['quit_electric_base'],
                'quit_water_base' => $data['quit_water_base'],
            ];
            $roomId = $data['room_id'];
            $companyId = $data['company_id'];

            $status = Record::where('room_id', $roomId)
                ->where('company_id', $companyId)
                ->where('in_use', 1)
                ->update($insert);

            if ($status) {
                return Room::where('room_id', $roomId)->update(['company_id' => 0]);
            }
        } else {
            $this->errorMsg = '失败：此房间是空房间！';
        }
        return false;
    }

    /**
     * 检查房间是否已被占用
     */
    private function checkRoomIsNotEmpty($roomId)
    {
        $room = Room::select('company_id')->find($roomId);
        if ($room->company_id > 0) { //已被占用
            return true;
        }
        return false;
    }
}
