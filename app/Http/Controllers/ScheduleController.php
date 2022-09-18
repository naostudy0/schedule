<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Calendar\CalendarView;
use App\Plan\PlanView;

class ScheduleController extends Controller
{
    /**
     * スケジュール一覧
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\View\View
     */
    public function show(Request $request)
    {
        $date = $request->input("date");

        if ($date && preg_match("/^[0-9]{4}-[0-9]{2}$/", $date)){
            $date = $date . "-15";
            $date = strtotime($date);
        } else {
            $date = time();
        }

        $calendar = new CalendarView($date);
        $year_month = date('Y-m',$date);

        $plan_view = new PlanView($year_month);
        $plans = $plan_view->getPlans();
        $plan_days = $plan_view->getPlanDays();
        $plan_colors = $plan_view->getPlanColors();

        return view('schedule.schedule', [
            "calendar" => $calendar,
            "plans" => $plans,
            "plan_days" => $plan_days,
            'plan_colors' => $plan_colors,
        ]);
    }
}
