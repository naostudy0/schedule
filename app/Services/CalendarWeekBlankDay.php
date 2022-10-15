<?php

namespace App\Services;

/**
 * 前月と翌月の日付をカレンダーに表示させないようにするためのクラス
 */
class CalendarWeekBlankDay extends CalendarWeekDay
{
    /**
     * クラス名を返す
     * 
     * @return string
     */
    public function getClassName()
    {
        return "day-blank";
    }

    /**
     * 予定新規作成のリンクと日付のクラス名を返す
     * 
     * @return string
     */
    public function render()
    {
        return '';
    }
}
