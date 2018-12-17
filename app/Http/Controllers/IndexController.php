<?php

namespace App\Http\Controllers;

use App\Model\Company;
use App\Model\Record;
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
        $count['居住用房']['1号楼'] = 10;

        $company_total_count = Record::where('in_use', 1)->count(DB::raw("distinct(`company_id`)"));
        $room_total_count = Record::where('in_use', 1)->count(DB::raw("distinct(`room_id`)"));
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
                $detail[$type][$building]['room_count'] = $roomCount;
                $detail[$type][$building]['company_count'] = $companyCount;
            }
        }

        return view('welcome', compact('detail', 'company_total_count', 'room_total_count'));
    }
}
