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

    protected array $days = [];
    protected object $start_day;
    protected object $last_day;
    protected object $tmp_day;
    protected object $day;

    /**
     * 1ヶ月分のカレンダーに表示される日付を返す（前月・翌月は空白）
     * 
     * @param int
     * @return array
     */
    public function getDays($count)
    {
        // 週の初めと週の終わりを設定(startOfWeek:月曜 endOfWeek:日曜なので1日引く)
        $this->start_day = $this->carbon->copy()->startOfWeek()->subDay(1);
        $this->last_day = $this->carbon->copy()->endOfWeek()->subDay(1);

        // 作業用
        $this->tmp_day = $this->start_day->copy();

        // 1週間繰り返し
        while($this->tmp_day->lte($this->last_day)){
            // 1ヶ月のカレンダーが6週で表示される場合は、6週目で処理がズレる場合があるので条件分岐する
            if ($count < 6 ){
                if($this->tmp_day->month == $this->carbon->month){
                    // 当月は日付を設定
                    $this->day = new CalendarWeekDay($this->tmp_day->copy());
                    $this->days[] = $this->day;
                    
                } else {
                    // 前月または翌月の場合は空白を設定
                    $this->day = new CalendarWeekBlankDay($this->tmp_day->copy());
                    $this->days[] = $this->day;
                }
            } else {
                // カレンダー上の6週目の日付は30または31なので、30以上は日付を表示する
                if ($this->tmp_day->day >= 30){
                    $this->day = new CalendarWeekDay($this->tmp_day->copy());
                    $this->days[] = $this->day;

                } else {
                    $this->day = new CalendarWeekBlankDay($this->tmp_day->copy());
                    $this->days[] = $this->day;
                }
            }
            //翌日に移動
            $this->tmp_day->addDay(1);
        }

        return $this->days;
    }
}