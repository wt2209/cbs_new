<?php

namespace App\Http\Controllers;

use App\Model\Company;
use App\Model\Record;
use App\Model\Repair;
use App\Model\Room;
use App\Model\RoomType;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class IndexController extends Controller
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
    public function getIndex(Request $request)
    {
        return view('index', ['user'=>$request->user()]);
    }

    public function getWelcome()
    {
        $company_total_count = Record::where('in_use', 1)->count(DB::raw("distinct(`company_id`)"));
        $room_total_count = Record::where('in_use', 1)->count(DB::raw("distinct(`room_id`)"));
        $repairing_count = Repair::where('is_printed', 1)->where('is_finished', 1)->count();
        $repair_current_count = Repair::where('is_finished', 1)
            ->where(DB::raw('YEAR(finished_at)'), date('Y'))
            ->where(DB::raw('MONTH(finished_at)'), date('m'))
            ->count();

        $types = RoomType::lists('type_name', 'id');
        $buildings = Room::distinct('building')->lists('building');

        $detail = [];
        foreach ($types as $typeId => $type) {
            foreach ($buildings as $building) {
                $roomIds = Room::where('type_id', $typeId)
                    ->where('building', $building)
                    ->lists('room_id')
                    ->toArray();

                $roomCount = Record::where('in_use', 1)->whereIn('room_id', $roomIds)->count(DB::raw("distinct(`room_id`)"));
                $companyCount = Record::where('in_use', 1)->whereIn('room_id', $roomIds)->count(DB::raw("distinct(`company_id`)"));
                if ($roomCount > 0 || $companyCount > 0) {
                    $detail[$type][$building]['room_count'] = $roomCount;
                    $detail[$type][$building]['company_count'] = $companyCount;
                }
            }
        }

        return view('welcome', compact(
            'detail',
            'company_total_count',
            'room_total_count',
            'repair_current_count',
            'repairing_count'
        ));
    }
}
