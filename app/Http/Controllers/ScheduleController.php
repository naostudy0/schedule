<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CalendarService;
use App\Models\Plan;
use App\Models\PlanShare;
use App\Models\User;
use Auth;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * @var \App\Models\Plan
     */
    private $plan;
    /**
     * @var \App\Models\PlanShare
     */
    private $plan_share;
    /**
     * @var \App\Models\User
     */
    private $user;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->plan = new Plan;
        $this->plan_share = new PlanShare;
        $this->user = new User;
    }

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

        $calendar = new CalendarService($date);

        // 自分の予定と共有された予定を取得
        $plans = $this->plan->getMyPlansAndSharedPlans(new Carbon($date), Auth::id());

        $days_have_plan = [];
        $shared_user_names = [];
        foreach ($plans as $plan) {
            $start_date = Carbon::parse($plan->start_datetime)->isoFormat('YYYY/MM/DD(ddd)');
            if (! in_array($start_date, $days_have_plan)) {
                // 予定のある日にちを設定
                $days_have_plan[] = $start_date;
            }

            $shared_user_ids = $plan->shared_user_ids ? explode(',', $plan->shared_user_ids) : false;
            if (! $shared_user_ids) {
                continue;
            }
            foreach ($shared_user_ids as $shared_user_id) {
                // 予定共有されているユーザー名を取得（GROUP CONCATで取得すると区切り文字を含む名前の場合に不具合が起きるため）
                $shared_user_names[$plan->plan_id][$shared_user_id] = $this->user->where('user_id', $shared_user_id)->value('name');
            }
        }

        return view('schedule.schedule', [
            'calendar' => $calendar,
            'plans' => $plans,
            'days_have_plan' => $days_have_plan,
            'shared_user_names' => $shared_user_names,
        ]);
    }
}
