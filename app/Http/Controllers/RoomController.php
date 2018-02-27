<?php

namespace App\Http\Controllers;

use App\Model\RoomType;
use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Model\Room;
use App\Model\Company;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    public function __construct()
    {
        $this->middleware('my.auth');
        $this->middleware('fieldFilter', ['only'=>['postStore']]);
    }

    /**
     * 房间明细
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getIndex(Request $request)
    {
        $structure = $this->getRoomStructure();
        $firstType = array_keys($structure)[0];
        $firstBuilding = $structure[$firstType][0];

        $currentType = $request->input('typename') ? $request->input('typename') : $firstType;
        $currentBuilding = $request->input('building') ? $request->input('building') : $firstBuilding;

        $type = RoomType::where('type_name', $currentType)->first();
        if ($type) {
            $rooms = Room::where('type_id', $type->id)
                ->where('building', $currentBuilding)
                ->get();
        }else {
            $rooms = collect([]);
        }
        $count = $this->roomsCount($rooms);

        $roomsWithFloor = $this->roomsGroupByFloor($rooms);

        $companies = Company::select('company_id', 'company_name')->get();
        return view('room.index', compact('structure', 'roomsWithFloor', 'currentBuilding', 'currentType', 'count', 'companies'));
    }

    /**
     * 按楼层分组
     * @param $rooms
     * @return mixed
     */
    private function roomsGroupByFloor($rooms)
    {
        // 按楼号 分组
        $roomsGroupByBuilding =  $rooms->groupBy(function ($room, $key) {
            return $room->building;
        });

        // 按楼层 分组
        return $roomsGroupByBuilding->map(function($building, $key) {
            return $building->groupBy(function($room, $k) {
                if(intval($room->room_name) > 0 && strlen($room->room_name) == 5) {
                    return substr($room->room_name, 1, 2);
                }
                return 'whatever';
            });
        });
    }

    /**
     * 获取房间与类型结构
     * @return array
     */
    private function getRoomStructure()
    {
        $rooms = Room::groupBy('type_id')
            ->groupBy('building')
            ->select('type_id', 'building')
            ->get();
        $result = [];
        foreach ($rooms as $room) {
            $result[$room->type->type_name][] = $room->building;
        }
        return $result;
    }


    /**
     * 搜索
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getSearch(Request $request)
    {
        $roomName = trim(strip_tags(htmlspecialchars($request->room_name)));
        $roomStatus = intval($request->room_status);

        if ($roomStatus === 1) {
            $model = Room::where('company_id', '<>', "0");
        } elseif ($roomStatus === 2) { //空房间。company_id=0
            $model = Room::where('company_id', "0");
        } else {
            $model = Room::where('company_id', '>=', 0);
        }
        if (!empty($roomName)) {
            $model->where('room_name', $roomName);
        }
        $rooms =  $model->get();
        $count = $this->roomsCount($rooms);

        $structure = $this->getRoomStructure();
        $roomsWithFloor = $this->roomsGroupByFloor($rooms);

        return view('room.index',  compact('structure', 'roomsWithFloor', 'count'));
    }

    /**
     * 添加房间
     * @return \Illuminate\View\View
     */
    public function getAdd()
    {
        return view('room.add');
    }

    public function getEdit($id)
    {
        $id = isset($id) ? (int)$id : 0;
        if (!$id)
            return redirect()->back();

        $room = Room::where('room_id', $id)->first();

        return view('room.edit', ['room'=>$room]);
    }

    /**
     * 根据room_id删除数据
     * @param Request $request
     */
    // TODO
    public function getRemove(Request $request)
    {
        //验证房间id
        $roomId = (int)$request->delete_id;
        if (!$roomId) {
            exit();
        }
        
        //若当前房间有人居住，则禁止删除
        if (Room::where('room_id', $roomId)
                ->where('company_id', '>', 0)
                ->count() > 0) {
            exit(json_encode(['message'=>'失败：此房间正在使用，不能删除！', 'status'=>0]));
        }

        if (Room::destroy($roomId)) {
            exit(json_encode(['message'=>'删除成功！', 'status'=>1]));
        } else {
            exit(json_encode(['message'=>'失败：删除数据时发生错误，请重试...', 'status'=>0]));
        }
    }

    /**
     * 新增数据
     * @param Request $request
     */
    public function postStore(Request $request)
    {
        //字段验证
        $validator = Validator::make($request->all(), [
            'room_id'=>'integer|min:1',
            'room_name' => 'required'
        ]);

        //验证不通过，返回第一个错误信息
        if ($validator->fails()) {
            exit(json_encode(['message'=>$validator->errors()->first(), 'status'=>0]));
        }

        //检测是否已经录入过此房间
        $count = Room::where('room_name', $request->room_name)
            ->count();
        //若添加的房间已存在，则返回错误
        if ($count > 0) {
            exit(json_encode(['message'=>'失败：此房间已经存在!', 'status'=>0]));
        }

        //新建模型
        $room = new Room();
        $room->room_name = $request->room_name;
        $room->room_type = $request->room_type;
        $room->room_remark = $request->room_remark;

        if ($room->save()) {
            exit(json_encode(['message'=>'操作成功！', 'status'=>1]));
        } else {
            exit(json_encode(['message'=>'失败：数据添加失败，请重试...', 'status'=>0]));
        }
    }

    public function postUpdate(Request $request)
    {
        $roomId = intval($request->room_id);
        $roomRemark = $request->input('room_remark');

        $status = DB::table('room')->where('room_id', $roomId)->update(['room_remark'=>$roomRemark]);
        if ($status) {
            return response()->json(['message'=>'操作成功！', 'status'=>1]);
        }
        return response()->json(['message'=>'失败：数据添加失败，请重试...', 'status'=>0]);
    }

    /**
     * 统计房间总数及空房间数
     * @param $rooms
     * @return mixed
     */
    private function roomsCount($rooms)
    {
        $count['used'] = $rooms->sum(function ($room) {
            return $room['company_id'] > 0 ? 1 : 0;
        });
        $count['total'] = $rooms->count();
        $count['empty'] = $count['total'] - $count['used'];
        return $count;
    }


    /**
     * 导出房间明细
     */
    public function getExport()
    {
        $rooms = Room::with('company')->get();
        $this->exportFile($rooms);
    }

    private function exportFile($rooms)
    {
        $filename = '房间明细-'.date('Ymd');
        //标题行
        $titleRow = [$filename];
        //菜单第一行
        $menuRow = ['序号','类型','楼号','房间名','状态','所属公司','性别','公司联系人','联系人电话','房间备注'];
        $data = [
            $titleRow,
            $menuRow,
        ];
        // 序号
        $serialNumber = 1;
        foreach ($rooms as $room) {
            if ($room->company_id > 0) {
                $tmp = [
                    $serialNumber++,
                    $room->type->type_name,
                    $room->building,
                    $room->room_name,
                    '正在使用',
                    $room->company->company_name,
                    $room->gender == 1 ? '男': '女',
                    $room->company->linkman,
                    $room->company->linkman_tel,
                    $room->room_remark
                ];
            } else {
                $tmp = [
                    $serialNumber++,
                    $room->type->type_name,
                    $room->building,
                    $room->room_name,
                    '空房间',
                    '',
                    '',
                    '',
                    '',
                    $room->room_remark
                ];
            }
            $data[] = $tmp;
        }
        ExcelController::exportFile($filename, $data, '房间明细');
    }
}