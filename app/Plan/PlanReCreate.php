<?php

namespace App\Plan;

use App\Plan\PlanColor;

class PlanReCreate
{
    private array $plan;
    private object $plan_color;
    private array $recreate_data;
    private array $color_checked;
    private int $color_number;

    /**
     * 一度入力されたデータが初期値として表示されるよう設定する
     * 
     * @param object
     */
    public function __construct($request)
    {
        $this->recreate_data = $request->toArray();
        $this->recreate_data['cancel_date'] = substr($this->recreate_data['start_date'], 0, 7);

        $this->plan_color = new PlanColor;
        $this->color_checked = $this->plan_color->getRedisplayData($this->recreate_data['color']);
    }

    /**
     * 入力されたデータを再表示できるよう設定したデータを配列で返す（「タグ」のラジオボタンを除く）
     * 
     * @return array
     */
    public function getData()
    {
        return $this->recreate_data;
    }

    /**
     * 選択された「タグ」のラジオボタンを再表示できるよう設定したデータを配列で返す
     * 
     * @return array
     */
    public function getColor()
    {
        return $this->color_checked;
    }
}