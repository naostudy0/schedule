<?php

namespace App\Calendar;

use Carbon\Carbon;

class CalendarWeek
{
    protected object $carbon;
    protected int $index;

    /**
     * Carbonインスタンス作成と、$indexを受け取る
     * 
     * @param object $date
     * @param int $index
     */
    public function __construct($date, $index)
    {
        $this->carbon = new Carbon($date);
        $this->index = $index;
    }

    public function getClassName()
    {
        return "week-" . $this->index;
    }

    protected array $days;
    protected object $start_day;
    protected object $last_day;
    protected object $tmp_day;
    protected object $day;

    /**
     * 1ヶ月分のカレンダーに表示される日付を返す（前月・翌月は空白）
     * 
     * @return array
     */
    public function getDays()
    {
        // 週の初めと週の終わりを設定
        $this->start_day = $this->carbon->copy()->startOfWeek();
        $this->last_day = $this->carbon->copy()->endOfWeek();

        // 作業用
        $this->tmp_day = $this->start_day->copy();

        // 1ヶ月繰り返し
        while($this->tmp_day->lte($this->last_day)){

            // 前月または翌月の場合は空白を設定
            if($this->tmp_day->month != $this->carbon->month){
                $this->day = new CalendarWeekBlankDay($this->tmp_day->copy());
                $this->days[] = $this->day;

            } else {
            // 当月は日付を設定
                $this->day = new CalendarWeekDay($this->tmp_day->copy());
                $this->days[] = $this->day;
            }
            //翌日に移動
            $this->tmp_day->addDay(1);
        }

        return $this->days;
    }
}