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

        $company = new Repair();
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

    /**
     * 待审核项目
     */
    public function getUnderReview()
    {
        $underReviews = Repair::where('is_reviewed', 0)->get();
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
            ->get();

        return view('repair.underFinish', compact('items'));
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

            if ($repair->save()) {
                return response()->json(['message'=>'操作成功！', 'status'=>1]);
            }
        }
        return response()->json(['message'=>'错误：保存失败...', 'status'=>0]);
    }

    /**
     * 已完工项目
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getFinished(Request $request)
    {
        $year = empty($request->input('year')) ? date('Y') : (int)$request->input('year');
        $month = empty($request->input('month')) ? date('m') : (int)$request->input('month');

        $items = Repair::where('is_reviewed', 1)
            ->where('is_passed', 1)
            ->where('is_finished', 1)
            ->where('finished_at', '>=', date('Y-m-d', strtotime($year.'-'.$month.'-1')))
            ->where('finished_at', '<', date('Y-m-d', strtotime($year.'-'.($month + 1).'-1')))
            ->get();

        return view('repair.finished', compact('items'));
    }


}
