<?php

namespace App\Calendar;

use Carbon\Carbon;
use Yasumi\Yasumi;

class CalendarWeekDay 
{
    protected object $carbon;
    protected object $holidays;
    protected bool $holiday_check;

    /**
     * Carbonインスタンス作成と祝日判定
     * 
     * @param object
     */
    public function __construct($date)
    {
        $this->carbon = new Carbon($date);

        $this->holidays = Yasumi::create("Japan", $this->carbon->format('Y') ,"ja_JP");
        $this->holiday_check = $this->holidays->isHoliday($this->carbon);
    }

    /**
     * 曜日・祝日のクラス名を返す
     * 
     */
    public function getClassName()
    {
        if(!$this->holiday_check){
            return "day-" . strtolower($this->carbon->format("D"));
        } else {
            return "day-" . strtolower($this->carbon->format("D")) . " " . "holiday";
        }
    }

    /**
     * 予定新規作成のリンクと日付のクラス名を返す
     * 
     */
    public function render()
    {
        return '<a href="plan/create?date=' .$this->carbon->format("Y-m-d") . '"><p class="day">' . $this->carbon->format("j"). '</p></a>';
    }
}