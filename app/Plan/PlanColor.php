<?php

namespace App\Plan;

class PlanColor
{
    /**
     * 渡された番号のカラーをcheckedにして配列を返す
     * 
     * @param  int
     * @return array
     */
    public function getColor(int $color_number = 1)
    {
        // 使用できる色の数
        $colors_count = count(config('const.plan_color'));

        for ($i = 1; $i <= $colors_count; $i++) {
            $color_checked[$i] = $i === $color_number ? 'checked' : ''; 
        }

        return $color_checked;
    }
}
