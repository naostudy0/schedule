<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlanRequest;
use App\Plan\LayoutSet\LayoutSet;
use App\Plan\OperationDatabase\PlanUpdate;
use App\Share\ShareIdChange;
use App\Share\ShareIdNameGet;
use Illuminate\Http\Request;
use App\Models\Plan;
use Auth;

class PlanController extends Controller
{
    /**
     * @var \App\Models\Plan
     */
    private $plan;
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
    }

    /**
     * 予定作成画面
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\View\View
     */
    public function planCreate(Request $request)
    {
        $plan_data = $this->initialize($request);

        $color_checked = $this->getColor();

        return view('plan.create', [
            'plan_data' => $plan_data,
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

        // 予定共有するにチェックされているが、共有するユーザーが指定されていない場合は共有しないに変更
        $plan_data['share_user'] ?? $plan_data['share_users'] = 0;

        if ($plan_data['share_users'] == 1){
            foreach($plan_data['share_user'] as $share_id){
                $share_id_change = new ShareIdChange($share_id);
                $share_user_name[] = $share_id_change->getName();
            }
        } else {
            $share_user_name = null;
        }

        $layout_set = new LayoutSet($plan_data);

        return view('plan.create_confirm', [
            'plan_data' => $plan_data,
            'layout_set' => $layout_set,
            'share_user_name' => $share_user_name,
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
        $plan_data['cancel_date'] = substr($plan_data['start_date'], 0, 7);
        $plan_data['share_users'] = $this->getShareUserData();

        $color_checked = $this->getColor($request->input('color'));

        return view('plan.create',[
            'plan_data' => $plan_data,
            'color_checked' => $color_checked,
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
        // 予定が存在していれば予定IDを取得
        $plan_id = $this->plan->getIdIfExists($request->input('id'), Auth::id());
        if (! $plan_id) {
            return redirect()->route('schedule')->with('flash_msg', $this->not_exist);
        }

        $plan_data = $this->plan->getOneRecord($plan_id);

        $plan_data['share_users'] = $this->getShareUserData();

        $color_checked = $this->getColor($plan_data['color']);

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
        $layout_set = new LayoutSet($plan_data);

        $exist = array_key_exists('share_user', $plan_data);
        $share_user_name = null;
        if ($exist) {
            foreach ($plan_data['share_user'] as $share_id) {
                $share_id_change = new ShareIdChange($share_id);
                $share_user_name[] = $share_id_change->getName();
            }
        }

        return view('plan.update_confirm', [
            'plan_data' => $plan_data,
            'layout_set' => $layout_set,
            'share_user_name' => $share_user_name,
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
        $plan_data['cancel_date'] = substr($plan_data['start_date'], 0, 7);
        $plan_data['share_users'] = $this->getShareUserData();
        $color_checked = $this->getColor($request->input('color'));

        return view('plan.update',[
            'plan_data' => $plan_data,
            'color_checked' => $color_checked,
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
        $plan_update = new PlanUpdate($request);
        $result = $plan_update->getResult();
        $date_redirect = $plan_update->getRedirectDate();

        \Session::flash('flash_msg', $result);
        return redirect()->to($date_redirect);
    }

    /**
     * 削除内容確認画面
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function deleteConfirm(Request $request)
    {
        // 予定が存在していれば予定IDを取得
        $plan_id = $this->plan->getIdIfExists($request->input('id'), Auth::id());
        if (! $plan_id) {
            return redirect()->route('schedule')->with('flash_msg', $this->not_exist);
        }

        $plan_data = $this->plan->getOneRecord($plan_id);
        $layout_set = new LayoutSet($plan_data);

        if ($plan_data['share_user_id']) {
            $plan_data['share_users'] = 1;
            $share_id_name_get = new ShareIdNameGet($plan_data['share_user_id']);
            $share_user_name = $share_id_name_get->getName();
        } else {
            $plan_data['share_users'] = 0;
            $share_user_name = null;
        }

        return view('plan.delete_confirm', [
            'plan_data' => $plan_data,
            'layout_set' => $layout_set,
            'share_user_name' => $share_user_name,
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
        // 予定が存在していれば予定IDを取得
        $plan_id = $this->plan->getIdIfExists($request->input('id'), Auth::id());
        if (! $plan_id) {
            return redirect()->route('schedule')->with('flash_msg', $this->not_exist);
        }

        // リダイレクト先の取得のために削除前にレコードを取得
        $row = $this->plan->getOneRecord($plan_id);
        // リダイレクト先の取得
        $redirect_route = $this->getRedirectRoute($row['start_date']);

        $this->plan->where('id', $plan_id)->delete();

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
        // 使用できる色の数
        $colors_count = count(config('const.plan_color'));

        for ($i = 1; $i <= $colors_count; $i++) {
            $color_checked[$i] = $i === $color_number ? 'checked' : '';
        }

        return $color_checked;
    }

    /**
     * 予定作成画面のパラメータ初期値
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    private function initialize($request)
    {
        $initialize = [
            'start_date'  => '',
            'start_time'  => '00:00',
            'end_date'    => '',
            'end_time'    => '00:00',
            'content'     => '',
            'detail'      => '',
            'cancel_date' => '',
        ];

        // クエリパラメータのチェック
        $date = $request->input('date');

        // 存在している日付か確認するために分割
        if ($date) {
            list($year, $month, $day) = explode('-', $date);
            // 予定入力前に表示していた月に戻れるように「キャンセル」用のdate作成
            $cancel_check = $year . '-' . $month;

            // 年月のデータが正しければ変数を更新
            if ($date && preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $date) && checkdate($month, $day, $year)) {
                $initialize['start_date']  = $date;
                $initialize['end_date']    = $date;
                $initialize['cancel_date'] = $cancel_check;
            }
        }

        $initialize['share_users'] = $this->getShareUserData();

        return $initialize;
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

    /**
     * 予定を共有しているユーザーの情報を取得
     * 
     * @return null|array
     */
    private function getShareUserData()
    {
        $user = Auth::user();
        $share_user_id = $user->share_user_id;

        if (! $share_user_id) {
            return;
        }

        // share_idとnameを配列で取得
        $share_id_name_get = new ShareIdNameGet($share_user_data);
        $this->share_users = $share_id_name_get->getData();
    }
}
