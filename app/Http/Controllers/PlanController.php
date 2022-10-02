<?php

namespace App\Http\Controllers;

use Auth;
use App\Http\Requests\PlanRequest;
use App\Models\Plan;
use App\Models\ShareUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PlanController extends Controller
{
    /**
     * @var \App\Models\Plan
     */
    private $plan;
    /**
     * @var \App\Models\User
     */
    private $user;
    /**
     * @var \App\Models\ShareUser
     */
    private $share_user;
    /**
     * @var string
     */
    private $not_exist = '該当の予定が見つかりませんでした。';

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->plan = new Plan;
        $this->user = new User;
        $this->share_user = new ShareUser;
    }

    /**
     * 予定作成画面
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\View\View
     */
    public function planCreate(Request $request)
    {
        $date = $request->input('date');
        $validator = Validator::make($request->only('date'), [
            'date' => 'required|date_format:Y-m-d'
        ]);
        if ($validator->fails()) {
            $date = Carbon::today()->format('Y-m-d');
        }
        $datetime = $date . ' 00:00';

        // 色選択の初期値
        $color_checked = $this->getColor();
        $share_users = $this->share_user->getShareUserData(Auth::id());

        return view('plan.create', [
            'plan_data' => [
                'start_datetime' => $datetime,
                'end_datetime'   => $datetime,
                'detail' => '',
                'content' => '',
            ],
            'share_users' => $share_users,
            'color_checked' => $color_checked,
        ]);
    }

    /**
     * 登録内容確認画面
     *
     * @param  \App\Http\Requests\PlanRequest
     * @return \Illuminate\View\View
     */
    public function planConfirm(PlanRequest $request)
    {
        $plan_data = $request->all();

        $shared_user_names = [];
        if ($request->share_users == 1 && $request->share_user) {
            foreach ($plan_data['share_user'] as $share_id) {
                $shared_user_names[] = $this->user->where('share_id', $share_id)->value('name');
            }
        }

        return view('plan.create_confirm', [
            'plan_data' => $plan_data,
            'shared_user_names' => $shared_user_names,
        ]);
    }

    /**
     * 登録内容確認画面から予定作成画面の再表示
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\View\View
     */
    public function planReCreate(Request $request)
    {
        $plan_data = $request->toArray();
        $plan_data['start_datetime'] = $plan_data['start_date'] . ' ' . $plan_data['start_time'];
        $plan_data['end_datetime'] = $plan_data['end_date'] . ' ' . $plan_data['end_time'];

        $plan_data['cancel_date'] = substr($plan_data['start_date'], 0, 7);
        $share_users = $this->share_user->getShareUserData(Auth::id());

        $color_checked = $this->getColor($request->input('color'));

        return view('plan.create',[
            'plan_data' => $plan_data,
            'color_checked' => $color_checked,
            'share_users' => $share_users,
        ]);
    }

    /**
     * 予定保存処理
     *
     * @param  \App\Http\Requests\PlanRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function planStore(PlanRequest $request)
    {
        // 予定の保存
        $this->plan->storePlan($request, Auth::id());
        // リダイレクト先のURLを取得
        $date_redirect = $this->getRedirectRoute($request->start_date);

        return redirect($date_redirect)->with('flash_msg', '予定を登録しました。');
    }

    /**
     * 予定更新画面
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function planUpdate(Request $request)
    {
        $plan_id = $request->input('id');
        $exists = $this->plan->where('plan_id', $plan_id)->where('user_id', Auth::id())->exists();
        if (! $exists) {
            return redirect()->route('schedule')->with('flash_msg', $this->not_exist);
        }

        $plan_data = $this->plan->getOneRecord($plan_id);

        $plan_data->share_users = $this->share_user->getShareUserData(Auth::id());

        $color_checked = $this->getColor($plan_data->color);

        return view('plan.update', [
            'plan_data' => $plan_data,
            'color_checked' => $color_checked,
        ]);
    }

    /**
     * 更新内容確認画面
     *
     * @param  \App\Http\Requests\PlanRequest
     * @return \Illuminate\View\View
     */
    public function updateConfirm(PlanRequest $request)
    {
        $plan_data = $request->toArray();

        $shared_user_names = [];
        if ($request->share_users == 1 && $request->share_user) {
            foreach ($plan_data['share_user'] as $share_id) {
                $shared_user_names[] = $this->user->where('share_id', $share_id)->value('name');
            }
        }

        return view('plan.update_confirm', [
            'plan_data' => $plan_data,
            'shared_user_names' => $shared_user_names,
        ]);
    }

    /**
     * 修正内容確認画面から予定修正画面の再表示
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\View\View
     */
    public function planReUpdate(Request $request)
    {
        $plan_data = $request->toArray();
        $plan_data['start_datetime'] = $plan_data['start_date'] . ' ' . $plan_data['start_time'];
        $plan_data['end_datetime'] = $plan_data['end_date'] . ' ' . $plan_data['end_time'];

        $plan_data['cancel_date'] = substr($plan_data['start_date'], 0, 7);

        $share_users = $this->share_user->getShareUserData(Auth::id());
        $color_checked = $this->getColor($request->input('color'));

        return view('plan.update',[
            'plan_data' => $plan_data,
            'color_checked' => $color_checked,
            'share_users' => $share_users,
        ]);
    }

    /**
     * 予定更新処理
     *
     * @param  \App\Http\Requests\PlanRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStore(PlanRequest $request)
    {
        $exists = $this->plan->where('plan_id', $request->id)->where('user_id', Auth::id())->exists();
        if (! $exists) {
            return redirect()->back()->with('flash_msg', '該当の予定が見つかりません');
        }

        $this->plan->updatePlan($request, Auth::id());
        $redirect_route = $this->getRedirectRoute($request->input('start_date'));

        return redirect($redirect_route)->with('flash_msg', '予定を更新しました');
    }

    /**
     * 削除内容確認画面
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function deleteConfirm(Request $request)
    {
        $plan_id = $request->input('id');
        $exists = $this->plan->where('plan_id', $plan_id)->where('user_id', Auth::id())->exists();
        if (! $exists) {
            return redirect()->route('schedule')->with('flash_msg', $this->not_exist);
        }

        $plan_data = $this->plan->getOneRecord($plan_id);

        $shared_user_names = [];
        $shared_user_ids = $plan_data->shared_user_ids ? explode(',', $plan_data->shared_user_ids) : false;
        if ($shared_user_ids) {
            foreach ($shared_user_ids as $shared_user_id) {
                // 予定共有されているユーザー名を取得（GROUP CONCATで取得すると区切り文字を含む名前の場合に不具合が起きるため）
                $shared_user_names[$shared_user_id] = $this->user->where('id', $shared_user_id)->value('name');
            }
        }

        return view('plan.delete_confirm', [
            'plan_data' => $plan_data,
            'shared_user_names' => $shared_user_names,
        ]);
    }

    /**
     * 予定削除処理
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteStore(Request $request)
    {
        $plan_id = $request->input('id');
        $exists = $this->plan->where('plan_id', $plan_id)->where('user_id', Auth::id())->exists();
        if (! $exists) {
            return redirect()->route('schedule')->with('flash_msg', $this->not_exist);
        }

        // リダイレクト先の取得のために削除前にレコードを取得
        $row = $this->plan->getOneRecord($plan_id);
        // リダイレクト先の取得
        $redirect_route = $this->getRedirectRoute($row->start_datetime);

        $this->plan->where('plan_id', $plan_id)->delete();

        return redirect($redirect_route)->with('flash_msg', '予定を削除しました');;
    }

    /**
     * 渡された番号のカラーをcheckedにして配列を返す
     *
     * @param  int
     * @return array
     */
    private function getColor(int $color_number = 1)
    {
        for ($i = 1; $i <= count(config('const.plan_color')); $i++) {
            $color_checked[$i] = $i === $color_number ? 'checked' : '';
        }

        return $color_checked;
    }

    /**
     * 予定開始日の月にリダイレクトするURLを取得
     * 
     * @param  string $start_date [予定開始日]
     * @return string
     */
    private function getRedirectRoute($start_date)
    {
        return '/schedule?date=' . substr($start_date, 0, 7);
    }
}
