<?php

namespace App\Calendar;

use Carbon\Carbon;
use Yasumi\Yasumi;

class CalendarWeekDay
{
    /**
     * @var \Carbon\Carbon
     */
    private $carbon;

    /**
     * Carbonインスタンス作成
     *
     * @param object
     */
    public function __construct($date)
    {
        $this->carbon = new Carbon($date);
    }

    /**
     * 曜日・祝日・当日を判定し、クラス名を返す
     *
     * @return string
     */
    public function getClassName()
    {
        // 祝日判定（クラスが複数になるため要半角スペース）
        $holidays = Yasumi::create('Japan', $this->carbon->format('Y'), 'ja_JP');
        $holiday = $holidays->isHoliday($this->carbon) ? ' holiday' : '';

        // 当日判定（クラスが複数になるため要半角スペース）
        $carbon_today = Carbon::today();
        $this->carbon->format('Ymd') == $carbon_today->format('Ymd') ? $today = " " . "today" : $today = '';

        return "day-" . strtolower($this->carbon->format("D")) . $holiday . $today;
    }

    /**
     * 予定新規作成のリンクと日付のクラス名を返す
     *
     * @return string
     */
    public function render()
    {
        return '<a href="plan/create?date=' . $this->carbon->format("Y-m-d") . '"><p class="day">' . $this->carbon->format("j"). '</p></a>';
    }
}
