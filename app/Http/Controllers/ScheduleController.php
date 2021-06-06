<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Calendar\CalendarView;
use Carbon\Carbon;
use App\Models\Plan;
use App\Plan\PlanGet;
use App\Plan\PlanView;
use Auth;

class ScheduleController extends Controller
{
    /**
     * スケジュールviewを返す
     * 
     * クエリパラメータにYYYY-mmが設定されている場合は該当の月、設定されていない場合は当月を表示する
     * 該当の月に登録されている予定を表示する
     * 
     * @param object
     */
    public function show(Request $request){

        $date = $request->input("date");
        
        if($date && preg_match("/^[0-9]{4}-[0-9]{2}$/", $date)){
            $date = $date . "-15";
            $date = strtotime($date);
        }else{
            $date = time();
        }

        $calendar = new CalendarView($date);
        
        $year_month = date('Y-m',$date);
        $plan_view = new PlanView($year_month);

        return view('schedule.schedule', [
            "calendar" => $calendar,
            'plan_view' => $plan_view,
        ]);
    }
}