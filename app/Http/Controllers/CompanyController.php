<?php

namespace App\Http\Controllers;

use App\Model\RoomType;
use DB;
use App\Model\Company;
use App\Model\Room;
use Illuminate\Http\Request;
use App\Http\Controllers\CompanyLogController;
use Route;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\ExcelController;

class CompanyController extends Controller
{
    /**
     * 原房间。用于日志记录
     * @var array
     */
    private $oldRooms = [];

    /**
     * 调整后的新房间。用于日志记录
     * @var array
     */
    private $newRooms = [];

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->middleware('my.auth');
        //使用中间件过滤字段
        $this->middleware('fieldFilter', ['only'=>['postStore']]);
    }

    /**
     * 首页
     * @return \Illuminate\View\View
     */
    public function getIndex()
    {
        $companiesCollection = Company::where('is_quit', 0)
            ->get();
        $companies = $this->companyRoomsCount($companiesCollection->toArray());
        $counts = $this->companyCount($companies);


        return view('company.index', compact('companies', 'counts'));
    }


    /**
     * 获取各公司所用房间数量
     * @param array $companies
     * @return array
     */
    private function companyRoomsCount(array $companies)
    {
        $counts = $this->roomsCountByCompanyId();
        foreach ($companies as $key => $company) {
            $id = $company['company_id'];
            $companies[$key]['count'] = isset($counts[$id]) ? $counts[$id] : [];
        }

        return $companies;
    }

    /**
     * 格式：
     * [
     *  'company_id'=>[
     *          '居住用房'=>27,
     *          '餐厅'=>5,
     *          '服务用房'=>3
     *      ]
     * ]
     * @return array
     */
    private function roomsCountByCompanyId()
    {
        $rooms = Room::where('company_id', '<>', 0)
            ->select('type_id','company_id')
            ->get();
        $types = RoomType::get();
        $typesById = $types->keyBy('id')->toArray();
        $result = [];
        foreach ($rooms as $room){
            $typeName = $typesById[$room->type_id]['type_name'];
            if (isset($result[$room->company_id][$typeName])) {
                $result[$room->company_id][$typeName]++;
            } else {
                $result[$room->company_id][$typeName] = 1;
            }
        }
        return $result;
    }


    /**
     * 计算共有多少个公司，占用多少房间
     * @param array $companies
     * @return array
     */
    private function companyCount(array $companies)
    {
        $count = ['total'=>count($companies)];
        foreach ($companies as $company) {
            if (empty($company['count'])) {
                continue;
            }

            foreach ($company['count'] as $typename => $c) {
                if (isset($count[$typename])) {
                    $count['rooms'][$typename] = $count['rooms'][$typename] + $c;
                } else {
                    $count['rooms'][$typename] = $c;
                }
            }

        }
        return $count;
    }

    /**
     * 搜索 只能同时搜索公司名或者人名
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function getSearch (Request $request)
    {
        $companyName = trim(strip_tags(htmlspecialchars($request->company_name)));
        $personName = trim(strip_tags(htmlspecialchars($request->person_name)));

        $whereArr = ['is_quit=0'];
        if (!empty($companyName)) {
            $whereArr[] = 'company_name like "%'.$companyName.'%"';
        } elseif (!empty($personName)) {
            $whereArr[] = '(linkman like "%'.$personName.'%" or manager like "%'.$personName.'%")';
        }
        $whereStr = implode(' and ', $whereArr);
        //导出文件
        if ($request->is_export == 1) {
            $companies = Company::whereRaw($whereStr)->get();
            $this->exportFile($companies);
            return response()->redirectTo('company/index');
        }
        $companies = Company::whereRaw($whereStr)->paginate(config('cbs.pageNumber'));


        $count = $this->companyCount($whereStr);
        return view('company.index', ['companies'=>$companies, 'count'=>$count]);
    }

    /**
     * 添加公司
     * @return \Illuminate\View\View
     */
    public function getAdd()
    {
        return view('company.addBasicInfo');
    }

    public function getEdit($companyId)
    {
        //验证company_id的合法性
        $this->validateCompanyId($companyId);

        $company = Company::findOrFail((int)$companyId);

        return view('company/edit', ['company'=>$company]);
    }

    /**
     * 存储公司基本数据
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function postStoreBasicInfo(Request $request)
    {
        //字段验证
        $validator = Validator::make($request->all(), [
            'company_id'=>'integer|min:1',
            'company_name' => 'required|between:1,255',
            'company_description'=>'between:1,255',
            'linkman'=>'required|between:1,5',
            'linkman_tel'=>'numeric',
            'manager'=>'between:1,5',
            'manager_tel'=>'numeric',
            'company_remark'=>'between:1,255',
            'type'=>'integer|min:1|max:3'
        ]);
        //验证不通过，返回第一个错误信息
        if ($validator->fails()) {
            exit(json_encode(['message'=>$validator->errors()->first(), 'status'=>0]));
        }

        $company = new Company();
        $company->company_name = $request->company_name;
        $company->company_description = $request->company_description;
        $company->linkman = $request->linkman;
        $company->linkman_tel = $request->linkman_tel;
        $company->manager = $request->manager;
        $company->manager_tel = $request->manager_tel;
        $company->company_remark = $request->company_remark;
        //开启事务
        DB::beginTransaction();
        if ($company->save()) {
            //主键
            $companyId = $company->getKey();
            //提交事务
            DB::commit();
            return $this->getSelectRooms($companyId);
        } else {
            //错误，回滚事务
            DB::rollBack();
            return response()->redirectTo(url('common/302'));
        }
    }
    public function postStoreEditInfo(Request $request)
    {
        //字段验证
        $validator = Validator::make($request->all(), [
            'company_id'=>'required|integer|min:1',
            'company_name' => 'required|between:1,255',
            'company_description'=>'between:1,255',
            'linkman'=>'required|between:1,5',
            'linkman_tel'=>'numeric',
            'manager'=>'between:1,5',
            'manager_tel'=>'numeric',
            'company_remark'=>'between:1,255',
            'type'=>'integer|min:1|max:3'
        ]);
        //验证不通过，返回第一个错误信息
        if ($validator->fails()) {
            exit(json_encode(['message'=>$validator->errors()->first(), 'status'=>0]));
        }

        $company = Company::find($request->company_id);
        if ($company) {
            $company->company_name = $request->company_name;
            $company->company_description = $request->company_description;
            $company->linkman = $request->linkman;
            $company->linkman_tel = $request->linkman_tel;
            $company->manager = $request->manager;
            $company->manager_tel = $request->manager_tel;
            $company->company_remark = $request->company_remark;
            //开启事务
            DB::beginTransaction();
            if ($company->save()) {
                //提交事务
                DB::commit();
                return response()->json(['message'=>'操作成功！', 'status'=>1]);
            } else {
                //错误，回滚事务
                DB::rollBack();
                //TODO  好好研究一下response  重构一下302 等问题
                return response()->json(['message'=>'失败：请重试！', 'status'=>0]);
            }
        }

    }

    /**
     * 公司入住时选择房间
     * @param $companyId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function getSelectRooms($companyId)
    {
        if (!$companyId) {
            return response()->redirectTo(url('common/302'));
        }
        return view('company.selectRooms',['company_id'=>$companyId]);
    }

    /**
     * 存储已经选好的房间
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postStoreSelectedRooms(Request $request)
    {
        print_r($request->rooms);die;


        //$roomDetails格式 : 1_2|2_1|3_1 ('room_id'_'gender')
        $this->newRooms = explode('|', htmlspecialchars(strip_tags($request->roomDetails)));
        $companyId = intval($request->company_id);
        //是否是新公司入住
        $isNewCompany = (isset($request->newCompany) && $request->newCompany == 1) ? 1 : 0;

        //旧房间
        $oldRooms = Room::where('company_id', $companyId)->get();
        foreach ($oldRooms as $oldRoom) {
            $this->oldRooms[] = $oldRoom->room_id . '_' .$oldRoom->gender;
        }

        // 清除所有旧房间
       Room::where('company_id', $companyId)->update([
            'company_id'=>0,
            'gender'=>1
       ]);

        //修改房间表
        foreach ($this->newRooms as $currentRoomDetail) {
            $currentRoomDetailArr = explode('_', $currentRoomDetail);
            Room::where('room_id', intval($currentRoomDetailArr[0]))
                ->update([
                    'company_id'=>$companyId,
                    'gender'=>intval($currentRoomDetailArr[1])
                ]);
        }

        CompanyLogController::log($companyId, $isNewCompany, $request->user()->id, $this->oldRooms, $this->newRooms);

        //下一步：存储变动房间的水电底数
        return response()->json(['message'=>'操作成功！', 'status'=>1]);
    }



    /**
     * 指定公司明细
     * @param $companyId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getCompanyDetail($companyId)
    {
        $company = Company::where('company_id', intval($companyId))
            ->where('is_quit', 0)
            ->first();

        if (!$company) {
            return response('未找到此房间', 404);
        }

        $rooms = Room::where('company_id', $companyId)->get();
        $company->detail = $this->setRoomsDetail($rooms);
        $company->count = $this->setPeopleCount($rooms);


        $types = $this->typeIdToTypeName();

        return view('company.companyDetail', compact('company', 'types'));
    }

    private function typeIdToTypeName()
    {
        $types = RoomType::get();
        $ret = [];
        foreach ($types as $type) {
            $ret[$type->id] = $type->type_name;
        }
        return $ret;
    }

    /**
     * 先按类型，再按人数组合出房间明细（groupBy）
     * @param $rooms
     * @return mixed
     */
    private function setRoomsDetail($rooms)
    {
        return $rooms->groupBy('type_id')->map(function($roomsByType, $typeId){
            $roomsByNumbers = $roomsByType->groupBy('person_number');
            return $roomsByNumbers->map(function($roomsByNumber, $personNumber) {
                return $roomsByNumber->implode('room_name', '&nbsp;&nbsp;&nbsp;');
            });
        });
    }

    /**
     * 按类型组合出 房间人次
     * @param $rooms
     * @return mixed
     */
    private function setPeopleCount($rooms)
    {
        return $rooms->groupBy('type_id')->map(function($roomsByType, $typeId){
            $roomsByNumbers = $roomsByType->groupBy('person_number');
            return $roomsByNumbers->map(function($roomsByNumber, $personNumber) {
                return $roomsByNumber->count();
            });


//            return $roomsByType->sum('person_number');
        });
    }

    /**
     * 显示公司尚未缴费的房间明细
     * @param $companyId
     * @return \Illuminate\View\View
     */
    public function getCompanyUtility($companyId)
    {
        $this->validateCompanyId($companyId);
        $utilities = DB::table('utility')
            ->join('company', 'company.company_id', '=', 'utility.company_id')
            ->join('room', 'room.room_id', '=', 'utility.room_id')
            ->where('utility.company_id', $companyId)
            ->where('is_charged', 0)
            ->get();

        $companyName = DB::table('utility')
            ->join('company', 'company.company_id', '=', 'utility.company_id')
            ->where('utility.company_id', $companyId)
            ->value('company_name');
        $count = [];
        $dateArr = [];

        if (count($utilities) > 0) {
            $count['water_money'] = $count['electric_money'] = 0;
            foreach ($utilities as $utility) {
                $dateArr[] = $utility->year . '-' . $utility->month;
                $count['water_money'] += $utility->water_money;
                $count['electric_money'] += $utility->electric_money;
            }
            $dateArr = array_unique($dateArr);
        }
        return view(
            'company.charge',
            [
                'utilities'=>$utilities,
                'date'=>implode('、', $dateArr),
                'count'=>$count,
                'company_id'=>$companyId,
                'company_name'=>$companyName
            ]
        );
    }

    /**
     * 公司所属房间批量缴费
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postCompanyUtilityCharge(Request $request)
    {
        $companyId = intval($request->company_id);
        if (!$companyId) {
            return response()->json(['message'=>'错误：参数错误！', 'status'=>0]);
        }
        $utilityIds = DB::table('utility')
            ->join('company', 'company.company_id', '=', 'utility.company_id')
            ->join('room', 'room.room_id', '=', 'utility.room_id')
            ->where('utility.company_id', $companyId)
            ->where('is_charged', 0)
            ->lists('utility_id');

        if (UtilityController::chargeStore($utilityIds)) {
            return response()->json(['message'=>'操作成功！', 'status'=>1]);
        }
        return response()->json(['message'=>'错误：请重试...', 'status'=>0]);
    }

    /**
     * 调整房间
     * @param $companyId
     * @return \Illuminate\View\View
     */
    public function getChangeRooms($companyId)
    {
        //验证company_id的合法性
        $this->validateCompanyId($companyId);

        $rooms = Room::where('company_id', (int)$companyId)
            ->get();
        $livingRooms = $diningRooms = $serviceRooms =[];
        foreach ($rooms as $room) {
            switch ($room->room_type) {
                case '1':
                    $livingRooms[] = $room;
                    break;
                case '2':
                    $diningRooms[] = $room;
                    break;
                case '3':
                    $serviceRooms[] = $room;
                    break;
            }
        }
        $company = Company::find($companyId);

        return view('company/changeRooms', [
            'livingRooms'=>$livingRooms,
            'diningRooms'=>$diningRooms,
            'serviceRooms'=>$serviceRooms,
            'company'=>$company
        ]);
    }

    public function getQuit($id)
    {
        // TODO 所有房间都要退房
        $company = Company::find(intval($id));
        $company->is_quit = 1;
        $company->save();
        return response()->json(['message'=>'操作成功！', 'status'=>1]);
    }

    private function validateCompanyId($companyId)
    {
        $companyId = (int) $companyId;
        if (!$companyId) {
            exit("<h2>参数错误</h2>");
        }
    }

    private function exportFile($companies)
    {
        $filename = '公司明细-'.date('Ymd');
        //标题行
        $titleRow = ['公司明细-'.date('Ymd')];
        //菜单第一行
        $menuRow = ['序号','公司名','描述','入住时间','日常联系人','联系人电话','公司负责人','负责人电话','备注'];
        $data = [
            $titleRow,
            $menuRow,
        ];
        // 序号
        $serialNumber = 1;
        foreach ($companies as $company) {
            $tmp = [
                $serialNumber++,
                $company->company_name,
                $company->company_description,
                substr($company->created_at,0,10),
                $company->linkman,
                $company->linkman_tel,
                $company->manager,
                $company->manager_tel,
                $company->company_remark,
            ];
            $data[] = $tmp;
        }
        ExcelController::exportFile($filename, $data);
    }

}