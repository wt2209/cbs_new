<?php

namespace App\Http\Controllers;

use App\Model\Repair;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class RepairController extends Controller
{
    public function __construct()
    {
        $this->middleware('my.auth');
    }

    public function getNotify()
    {
        $data['unprinted'] = Repair::where('is_printed', 0)
            ->where('is_passed', 1)
            ->where('is_printed', 0)
            ->where('is_finished', 0)
            ->where('canceled', 0)
            ->count();
        $data['unreviewed'] = Repair::where('is_reviewed', 0)
            ->where('is_finished', 0)
            ->count();
        $data['username'] = Auth::user()->user_name;
        return response()->json($data);
    }

    /**
     * 添加项目
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getCreate()
    {
        return view('repair.create');
    }

    /**
     * 存储报修项目
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postStore(Request $request)
    {
        //字段验证
        $validator = Validator::make($request->all(), [
            'location'=>'required|between:1,255',
            'content' => 'required|between:1,255',
            'name'=>'between:1,5',
            'phone_number'=>'numeric',
        ]);
        //验证不通过，返回第一个错误信息
        if ($validator->fails()) {
            exit(json_encode(['message'=>$validator->errors()->first(), 'status'=>0]));
        }

        $id = (int)$request->input('id');
        if ($id) {
            $company = Repair::find($id);
        } else {
            $company = new Repair();
        }
        $company->location = $request->input('location');
        $company->content = $request->input('content');
        $company->name = $request->input('name');
        $company->input_man = Auth::user()->user_name;
        $company->phone_number = $request->input('phone_number');
        if(strtotime($request->input('report_at'))) {
            $company->report_at = date('Y-m-d H:i:s', strtotime($request->input('report_at')));
        } else {
            $company->report_at = date('Y-m-d H:i:s');
        }

        if ($company->save()) {
            return response()->json(['message'=>'操作成功！', 'status'=>1]);
        }
        return response()->json(['message'=>'错误：数据添加失败，请重试...', 'status'=>0]);
    }


    public function getEdit($id)
    {
        $item = Repair::find((int)$id);
        return view('repair.edit', compact('item'));
    }


    /**
     * 待审核项目
     */
    public function getUnderReview()
    {
        $underReviews = Repair::where('is_reviewed', 0)->orderBy('id','desc')->get();
        return view('repair.underReview', compact('underReviews'));
    }



    /**
     * 审核
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getReview($id)
    {
        $item = Repair::find(intval($id));
        return view('repair.review', compact('item'));
    }

    /**
     * 审核单个项目并保存结果
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postReviewForOne(Request $request)
    {
        $id = (int) $request->input('id');
        if ($id) {
            $repair = Repair::find($id);

            if ($repair->is_reviewed == 1) {
                return response()->json(['message'=>'操作成功！', 'status'=>1]);
            }

            $repair->review_remark = $request->input('review_remark');
            $repair->is_passed = (int) $request->input('is_passed');
            $repair->is_reviewed = 1;
            $repair->reviewed_at = date('Y-m-d H:i:s');
            $repair->reviewer = Auth::user()->user_name;

            if ($repair->save()) {
                return response()->json(['message'=>'操作成功！', 'status'=>1]);
            }
        }
        return response()->json(['message'=>'错误：保存失败...', 'status'=>0]);
    }

    /**
     * 未完工项目
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getUnderFinish()
    {
        $items = Repair::where('is_reviewed', 1)
            ->where('is_passed', 1)
            ->where('is_finished', 0)
            ->where('canceled', 0)
            ->orderBy('id','desc')
            ->get();

        return view('repair.underFinish', compact('items'));
    }

    public function getPrint(Request $request)
    {
        $id = (int)$request->input('id');
        $item = Repair::find($id);
        if ($item->is_printed == 0) {
            $item->is_printed = 1;
            $item->printed_at = date('Y-m-d H:i:s');
            $item->save();
        }
        return response()->json(['status'=>1]);
    }

    /**
     * 项目完工界面
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getFinish($id)
    {
        $item = Repair::find(intval($id));
        return view('repair.finish', compact('item'));
    }

    /**
     * 单个项目完工，保存结果
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postFinishForOne(Request $request)
    {
        $id = (int) $request->input('id');
        if ($id) {
            $repair = Repair::find($id);

            if ($repair->is_finished == 1) {
                return response()->json(['message'=>'操作成功！', 'status'=>1]);
            }

            $repair->finish_remark = $request->input('finish_remark');
            $repair->is_finished = 1;
            $repair->finished_at = date('Y-m-d H:i:s');
            $repair->report_at = $repair->report_at;

            if ($repair->save()) {
                return response()->json(['message'=>'操作成功！', 'status'=>1]);
            }
        }
        return response()->json(['message'=>'错误：保存失败...', 'status'=>0]);
    }

    /**
     * 取消未完工项目
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCancel(Request $request)
    {
        $id = (int)$request->input('delete_id');
        if ($id) {
            $item = Repair::find($id);
            $item->canceled = 1;

            if ($item->save()) {
                return response()->json(['message'=>'操作成功！', 'status'=>1]);
            }
        }
        return response()->json(['message'=>'错误：保存失败...', 'status'=>0]);
    }

    /**
     * 已完工项目
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function getFinished(Request $request)
    {
        $yearMonth = $request->input('year_month');
        $arr = explode('-', $yearMonth);
        $year = (isset($arr[0]) && !empty($arr[0])) ? (int) $arr[0] : date('Y');
        $month = (isset($arr[1]) && !empty($arr[1])) ? (int) $arr[1] : date('m');

        $items = Repair::where('is_reviewed', 1)
            ->where('is_passed', 1)
            ->where('is_finished', 1)
            ->where('canceled', 0)
            ->whereRaw("YEAR(finished_at) = {$year}")
            ->whereRaw("MONTH(finished_at) = {$month}")
            ->orderBy('id','desc')
            ->get();

        //导出文件
        if ($request->input('is_export') == 1) {
            $this->exportFile($items);
            return response()->redirectTo('repair/finished');
        }

        return view('repair.finished', compact(['items', 'year', 'month']));
    }

    /**
     * 未通过项目
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getUnpassed()
    {
        $items = Repair::where('is_reviewed', 1)
            ->where('is_passed', 0)
            ->orderBy('id','desc')
            ->get();

        return view('repair.unpassed', compact('items'));
    }

    /**
     * 已取消项目
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getCanceled()
    {
        $items = Repair::where('is_reviewed', 1)
            ->where('is_passed', 1)
            ->where('canceled', 1)
            ->orderBy('id','desc')
            ->get();

        return view('repair.canceled', compact('items'));
    }

    public function getComment($id)
    {
        $item = Repair::find((int)$id);
        return view('repair.comment', compact('item'));
    }

    public function postCommentStore(Request $request)
    {
        $id = (int)$request->input('id');
        if ($id) {
            $item = Repair::find($id);
            $item->comment = $request->input('comment');
            if ($item->save()) {
                return response()->json(['message'=>'操作成功！', 'status'=>1]);
            }
        }
        return response()->json(['message'=>'错误：保存失败...', 'status'=>0]);
    }

    /**
     * 导出文件
     * @param $items
     */
    private function exportFile($items)
    {
        $filename = '维修项目明细-'.date('Ymd');
        //标题行
        $titleRow = ['维修项目明细-'.date('Ymd')];
        //菜单第一行
        $menuRow = ['序号','位置/房间号','报修内容','报修人','报修时间','审核人','审核时间','审核说明', '完工时间', '完工说明', '评价'];
        $data = [
            $titleRow,
            $menuRow,
        ];
        // 序号
        $serialNumber = 1;
        foreach ($items as $item) {
            $tmp = [
                $serialNumber++,
                $item->location,
                $item->content,
                $item->name,
                $item->report_at,
                $item->reviewer,
                $item->reviewed_at,
                $item->review_remark,
                $item->finished_at,
                $item->finish_remark,
                $item->comment,
            ];
            $data[] = $tmp;
        }
        ExcelController::exportFile($filename, $data);
    }
}
