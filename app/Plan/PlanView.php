<?php

namespace App\Plan;

use Illuminate\Support\Facades\Crypt;
use App\Plan\PlanGet;
use App\Share\ShareIdNameGet;
use App\Models\Plan;
use App\User;
use Auth;

class PlanView
{
    private $user;
    private $year_month;
    private $plans;
    private $plan;
    private $plan_colors = [];
    private $plan_days;
    private $plans_get;


    /**
     * 該当月の予定、予定がある日付、設定しているカラーを取得
     * 
     * @param string
     */
    public function __construct($year_month)
    {
        $this->user = Auth::id();

        // ------- 予定 ------

        // 該当月の予定を日付順・開始時間順で取得
        $this->plans_get = Plan::where('user_id', $this->user)->where('start_date', 'like', "%$year_month%")->orderBy('start_date','asc')->orderBy('start_time','asc')->get();
        $plans_array = $this->plans_get->toArray();

        // 他のユーザーが自分と共有している予定を取得（「,id,」で検索）
        $search = ',' . $this->user . ',';
        $plans_share_get = Plan::where('start_date', 'like', "%$year_month%")->where('share_user_id', 'like', "%$search%")->get();
        $plans_share = $plans_share_get->toArray();

        // 自分が登録した予定と、自分と共有している予定を結合
        $this->plans = array_merge($plans_array, $plans_share);

        // 内容・詳細を複合化
        $count = 0;
        foreach ($this->plans as &$plan) {
            $user = User::where('id', $plan['user_id'])->first();
            $plan['name'] = $user['name'];

            $plan['content'] = Crypt::decrypt($plan['content']);

            if( $plan['detail'] ){
                $plan['detail'] = Crypt::decrypt($plan['detail']);

            } else {
                $detail = '';
            }

            $share_user_id = $plan['share_user_id'];
            if($share_user_id) {
                $share_id_name_get = new ShareIdNameGet($share_user_id);
                $plan['share_users'] = $share_id_name_get->getData();
            }

            $count++;
        }

        // 予定がない場合、空の配列になりエラーが発生するため、予定がある場合のみ実行する
        if(!$this->plans === []){
            // 開始日を基準に昇順で並び替え
            $this->plans = $this->sortByKey('start_date', SORT_ASC, $this->plans);
        }

        // ------- 日付 ------

        // 該当月の予定のある日付を取得
        $this->plan_days_get = Plan::where('user_id', $this->user)->where('start_date', 'like', "%$year_month%")->groupBy('start_date')->get('start_date');
        $plan_days_array = $this->plan_days_get->toArray();

        // 他のユーザーが自分と共有している予定の日付を取得（「,id,」で検索）
        $plan_share_days_get = Plan::where('start_date', 'like', "%$year_month%")->where('share_user_id', 'like', "%$search%")->groupBy('start_date')->get('start_date');
        $plans_share_array = $plan_share_days_get->toArray();

        // 自分が登録した日付の配列と、他のユーザーが自分と共有している予定の日付の配列を結合
        $plan_days_merge =  array_merge($plan_days_array, $plans_share_array);
        // 重複削除
        $this->plan_days = array_unique($plan_days_merge, SORT_REGULAR);
        // 日付を昇順で並び替え
        array_multisort($this->plan_days);

        // 曜日
        $week_number = [
            '(日)', //0
            '(月)', //1
            '(火)', //2
            '(水)', //3
            '(木)', //4
            '(金)', //5
            '(土)', //6
        ];

        // 日付に曜日のデータを追加
        $days_count = 0;
        foreach ($this->plan_days as $plan_day) {
            $day = str_replace('-', '', $plan_day['start_date']);
            $date_number = date('w', strtotime($day));
            $this->plan_days[$days_count]['day_of_the_week'] = $week_number[$date_number]; 
            $days_count++;
        }


        // ------- カラー ------

        // planテーブルcolorカラムに設定できる定数・名前を取得
        $tag_colors = config('const.PLAN_COLOR');
        
        foreach($this->plans as &$this->plan){
            // 修正・削除時に使用するidを暗号化
            $encrypted = encrypt($this->plan['id']);
            $this->plan['id'] = $encrypted;

            // 定数から名前に変換
            $color_index = $this->plan['color'];
            $tag_color = array_search($color_index, $tag_colors);
            $this->plan['color'] = $tag_color;
            $this->plan_colors[$color_index] = $tag_color;
            unset($this->plan);
        }

        // 該当月に使用されている色を定数の昇順に並び替え
        ksort($this->plan_colors);
    }

    /**
     * 該当月の予定を日付順・開始時間順で返す
     * 
     * @return array
     */
    public function getPlans()
    {
        return $this->plans;
    }

    /**
     * 該当月の予定のある日付を返す
     * 
     * @return array
     */
    public function getPlanDays()
    {
        return $this->plan_days;
    }

    /**
     * 該当月に使用されている色を定数の昇順で返す
     * 
     * @return array
     */
    public function getPlanColors()
    {
        return $this->plan_colors;
    }

    /**
     * 多次元配列を一つのキーの値を基準に並び替える
     * 
     * @param string
     * @param string
     * @param array
     * 
     * @return array
     */
    function sortByKey($key_name, $sort_order, $array) {
        foreach ($array as $key => $value) {
            $standard_key_array[$key] = $value[$key_name];
        }
    
        array_multisort($standard_key_array, $sort_order, $array);
    
        return $array;
    }
}