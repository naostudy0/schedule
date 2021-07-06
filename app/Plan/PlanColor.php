<?php

namespace App\Plan;

class PlanColor
{
    private $color_checked;
    private $i;

    /**
     * ラジオボタンのチェック判定用の変数を初期化
     * 
     */
    public function __construct()
    {
        for ($this->i = 1; $this->i <= 6; $this->i++) {
            $this->color_checked[$this->i] = ''; 
        }
    }

    /**
     * カラー1をcheckedにして配列を返す
     * 
     * @return array
     */
    public function getInitialize()
    {
        $this->color_checked[1] = 'checked';
        return $this->color_checked;
    }

    /**
     * 渡された番号のカラーをcheckedにして配列を返す
     * 
     * @param int
     * @return array
     */
    public function getRedisplayData($color_number)
    {
        $this->color_checked[$color_number] = 'checked';
        return $this->color_checked;
    }
}