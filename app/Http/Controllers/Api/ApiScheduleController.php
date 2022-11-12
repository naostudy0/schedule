<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
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
        // 予定に使用可能な色
        $plan_colors = config('const.plan_color');
        // jsはキーと値を入れ替えるメソッドがないのでphpで処理
        $plan_colors_flip = array_flip($plan_colors);
        if ($plans->isEmpty()) {
            return response()->json([
                'planData' => [],
                'sharedUserNames' => [],
                'colors' => $plan_colors,
                'colorsFlip' => $plan_colors_flip,
            ]);
        }

        $shared_user_names = [];
        foreach ($plans as $plan) {
            $plan_data[] = [
                'planId'        => $plan->plan_id,
                'content'       => $plan->content,
                'detail'        => $plan->detail,
                'startDatetime' => $plan->start_datetime,
                'endDatetime'   => $plan->end_datetime,
                'color'         => $plan_colors_flip[$plan->color],
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
            'sharedUserNames' => $shared_user_names,
            'colors' => $plan_colors,
            'colorsFlip' => $plan_colors_flip,
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
        $result = ['result' => false];
        $user_id = Auth::id();
        if (! $user_id) {
            return response()->json($result);
        }

        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return response()->json($result);
        }

        DB::beginTransaction();
        try {
            // 予定保存
            $this->plan->storePlan($request, $user_id);
            DB::commit();
            $result = ['result' => true];
        } catch (\Exception $e) {
            DB::rollback();
        }

        return response()->json($result);
    }

    /**
     * バリデーター
     *
     * @param array $form_data [送信された値]
     */
    private function validator($form_data)
    {
        $start_date = strtotime($form_data["start_date"]);
        $end_date   = strtotime($form_data["end_date"]);

        // 終了日が開始日よりも後であれば開始時間と終了時間が前後しても良い
        return Validator::make($form_data, [
            'start_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'end_time'   => 'required|date_format:H:i' . ($start_date < $end_date ? '' : '|after_or_equal:start_time'),
            'color'      => 'required|integer|between:1,' . count(config('const.plan_color')),
            'content'    => 'required|max:128',
            'detail'     => 'max:255',
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $result = ['result' => false];
        $user_id = Auth::id();
        if (! $user_id) {
            return response()->json($result);
        }

        $exists = $this->plan->where('user_id', $user_id)->where('plan_id', $request->input('plan_id'))->exists();

        $validator = $this->validator($request->all());
        if (! $exists || $validator->fails()) {
            return response()->json($result);
        }

        DB::beginTransaction();
        try {
            // 予定更新
            $this->plan->updatePlan($request, $user_id);
            DB::commit();
            $result = ['result' => true];
        } catch (\Exception $e) {
            DB::rollback();
        }

        return response()->json($result);
    }

    /**
     * 予定削除
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = ['result' => false];
        $user_id = Auth::id();
        if (! $user_id) {
            return response()->json($result);
        }

        $this->plan->where('user_id', $user_id)
            ->where('plan_id', $id)
            ->delete();

        return;
    }
}
