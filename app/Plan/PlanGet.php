<?php

namespace App\Plan;

use App\Models\Plan;
use Auth;

class PlanGet
{
    private int $user;
    private string $year_month;
    private array $plans;
    private array $plan;
    private array $plan_colors = [];
    private array $plan_days;
    private object $plans_get;

    /**
     * 該当月の予定、予定がある日付、設定しているカラーを取得
     * 
     * @param string
     */
    public function __construct($year_month)
    {
        $this->user = Auth::id();

        // 該当月の予定を日付順・開始時間順で取得
        $this->plans_get = Plan::where('user_id', $this->user)->where('start_date', 'like', "%$year_month%")->orderBy('start_date','asc')->orderBy('start_time','asc')->get();
        $this->plans = $this->plans_get->toArray();

        // 該当月の予定のある日付を取得
        $this->plan_days_get = Plan::where('user_id', $this->user)->where('start_date', 'like', "%$year_month%")->groupBy('start_date')->get('start_date');
        $this->plan_days = $this->plan_days_get->toArray();

        // planテーブルcolorに設定できる定数・名前を取得
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
    protected function getPlans()
    {
        return $this->plans;
    }

    /**
     * 該当月の予定のある日付を返す
     * 
     * @return array
     */
    protected function getPlanDays()
    {
        return $this->plan_days;
    }

    /**
     * 該当月に使用されている色を定数の昇順で返す
     * 
     * @return array
     */
    protected function getPlanColors()
    {
        return $this->plan_colors;
    }
}