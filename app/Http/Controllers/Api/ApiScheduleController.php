<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\CalendarService;
use App\Models\Plan;
use App\Models\PlanShare;
use App\Models\User;
use Carbon\Carbon;
use Auth;

class ApiScheduleController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $date = $request->input("date");
        if ($date && preg_match("/^[0-9]{4}-[0-9]{2}$/", $date)){
            $date = $date . "-15";
            $date = strtotime($date);
        } else {
            $date = time();
        }

        // 自分の予定と共有された予定を取得
        $plans = $this->plan->getMyPlansAndSharedPlans(new Carbon($date), Auth::id());
        if ($plans->isEmpty()) {
            return [
                'planData' => [],
                'shared_user_names' => [],
            ];
        }

        $shared_user_names = [];
        foreach ($plans as $plan) {
            $plan_data[] = [
                'planId'        => $plan->plan_id,
                'content'       => $plan->content,
                'detail'        => $plan->detail,
                'startDatetime' => $plan->start_datetime,
                'endDatetime'   => $plan->end_datetime,
                'color'         => array_flip(config('const.plan_color'))[$plan->color],
            ];

            $shared_user_ids = $plan->shared_user_ids ? explode(',', $plan->shared_user_ids) : false;
            if (! $shared_user_ids) {
                continue;
            }
            foreach ($shared_user_ids as $shared_user_id) {
                // 予定共有されているユーザー名を取得（GROUP CONCATで取得すると区切り文字を含む名前の場合に不具合が起きるため）
                $shared_user_names[$plan->plan_id][$shared_user_id] = $this->user->where('user_id', $shared_user_id)->value('name');
            }
        }

        return response()->json([
            'planData' => $plan_data,
            'shared_user_names' => $shared_user_names,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}