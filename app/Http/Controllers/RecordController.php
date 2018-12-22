<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Record;
use App\Model\Room;
use App\Model\Company;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ExcelController;

class RecordController extends Controller
{

    /**
     * 错误提示信息
     */
    public $errorMsg = '失败：未知错误！';


    public function __construct()
    {
        $this->middleware('my.auth');
    }
    
    /**
     * 房间使用记录
     */
    public function getIndex()
    {
        $records = Record::with('company')->with('room')->orderBy('id', 'desc')->paginate(config('cbs.pageNumber'));
        $companies = Company::orderBy('company_name', 'asc')->get();

        return view('record.index', compact('records', 'companies'));
    }

    /**
     * 搜索
     */
    public function getSearch(Request $request)
    {
        $companyId = (int)$request->company_id;
        $room = $request->room;
        $inUse = (int)$request->in_use;

        $model = Record::with('company', 'room');

        if ($companyId !== 0) {
            $model = Record::where('company_id', $companyId);
        }

        if ($room) {
            $model->whereHas('room', function ($query) use ($room) {
                $query->where('room_name', $room);
            });
        }

        // [0,1]表示正在使用或已经退房
        if (in_array($inUse, [0, 1])) {
            $model->where('in_use', $inUse);
        }

        //导出文件
        if ($request->is_export == 1) {
            $records = $model->get();
            $this->exportFile($records);
            return response()->redirectTo('record/index')->withInput();
        }

        $records = $model->orderBy('id', 'desc')->paginate(config('cbs.pageNumber'));
        $companies = Company::orderBy('company_name', 'asc')->get();
        return view('record.index', compact('records', 'companies'));
    }

    /**
     * 修改
     */
    public function getEdit($id)
    {
        $record = Record::find((int) $id);
        if (!$record->belongs_to) {
            $record->belongs_to = $record->company->belongs_to;
            $record->save();
        }

        return view('record.edit', compact('record'));
    }

    /**
     * 存储修改的数据
     */
    public function postUpdate(Request $request)
    {
        $record = Record::find($request->id);
        $record->belongs_to = $request->belongs_to;
        $record->price = $request->price;
        $record->enter_electric_base = $request->enter_electric_base;
        $record->enter_water_base = $request->enter_water_base;
        $record->entered_at = $request->entered_at;
        if ($record->in_use === 0) {
            $record->quit_electric_base = $request->quit_electric_base;
            $record->quit_water_base = $request->quit_water_base;
            $record->quit_at = $request->quit_at;
        }

        $record->save();
        return response()->json(['message'=>'操作成功！', 'status'=>1]);
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
            $belongsTo = $tmp[4] == "修船" ? "修船" : "造船";
            $data = [
                'room_id' => $roomId,
                'company_id' => $companyId,
                'gender' => $gender,
                'enter_electric_base' => $enterElectricBase,
                'enter_water_base' => $enterWaterBase,
                'entered_at' => date('Y-m-d H:i:s'),
                'belongs_to' => $belongsTo,
            ];

            $this->addOneRecord($data);
        }
        return response()->json(['message'=>'操作成功！', 'status'=>1]);
    }

    /**
     * 批量入住
     */
    public function postMassComplete(Request $request)
    {
        $companyId = intval($request->company_id);
        $items = explode('|', $request->rooms);

        foreach ($items as $item) {
            $tmp = explode('_', $item);
            $roomId = (int) $tmp[0];
            $quitElectricBase = (int) $tmp[1];
            $quitWaterBase = (int) $tmp[2];
            $data = [
                'room_id' => $roomId,
                'company_id' => $companyId,
                'quit_electric_base' => $quitElectricBase,
                'quit_water_base' => $quitWaterBase,
                'quit_at' => date('Y-m-d H:i:s'),
            ];

            $this->completeOneRecord($data);
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
            'belongs_to' => $request->belongs_to == "修船" ? "修船" : "造船",
        ];
        if ($this->addOneRecord($data)){
            return response()->json(['message'=>'操作成功！', 'status'=>1]);
        }

        return response()->json(['message'=>$this->errorMsg, 'status'=>0]);
    }

    /**
     * 某个公司退租某个房间
     */
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

        if ($this->checkCompanyIsQuit($data['company_id'])) {
            $this->errorMsg = '失败：此公司已退租！';
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
                'quit_at' => $data['quit_at'],
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

    private function checkCompanyIsQuit($companyId)
    {
        $company = Company::find($companyId);
        if ($company->is_quit == 1) {
            return true;
        }
        return false;
    }

    /**
     * 导出文件
     */
    private function exportFile($records)
    {
        $filename = '公司入住记录-'.date('Ymd');
        //标题行
        $titleRow = ['公司入住记录-'.date('Ymd')];
        //菜单第一行
        $menuRow = ['序号','公司名','房间号','属于','性别','月租金','入住时间','退房时间','入住时电表','入住时水表', '退房时电表', '退房时水表'];
        $data = [
            $titleRow,
            $menuRow,
        ];
        // 序号
        $serialNumber = 1;
        foreach ($records as $record) {
            $tmp = [
                $serialNumber++,
                $record->company->company_name,
                $record->room->room_name,
                $record->belongs_to ? $record->belongs_to : $record->company->belongs_to,
                $record->gender,
                $record->price,
                $record->entered_at,
                $record->quit_at,
                $record->enter_electric_base,
                $record->enter_water_base,
                $record->quit_electric_base,
                $record->quit_water_base,
            ];
            $data[] = $tmp;
        }
        ExcelController::exportFile($filename, $data);
    }
}
