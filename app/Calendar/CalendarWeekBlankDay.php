<?php

namespace App\Calendar;

/**
 * 前月と翌月の日付をカレンダーに表示させないようにするためのクラス
 *
 */
class CalendarWeekBlankDay extends CalendarWeekDay
{
    public function getClassName()
    {
        return "day-blank";
    }

    public function render()
    {
        return '';
    }
}