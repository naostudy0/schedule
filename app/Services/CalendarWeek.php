<?php

namespace App\Services;

use Carbon\Carbon;

class CalendarWeek
{
    /**
     * @var \Carbon\Carbon
     */
    private $carbon;
    /**
     * @var int
     */
    private $index;

    /**
     * Carbonインスタンス作成と、$indexを受け取る
     * 
     * @param \Carbon\Carbon
     * @param int
     */
    public function __construct($date, $index)
    {
        $this->carbon = new Carbon($date);
        $this->index = $index;
    }

    /**
     * クラス名を返す
     * 
     * @return string
     */
    public function getClassName()
    {
        return "week-" . $this->index;
    }

    /**
     * 1ヶ月分のカレンダーに表示される日付を返す（前月・翌月は空白）
     * 
     * @param  int
     * @return array
     */
    public function getDays($count)
    {
        // 週の初めと週の終わりを設定(startOfWeek:月曜 endOfWeek:日曜なので1日引く)
        $start_day = $this->carbon->copy()->startOfWeek()->subDay(1);
        $last_day = $this->carbon->copy()->endOfWeek()->subDay(1);
        // 作業用
        $tmp_day = $start_day->copy();

        // 1週間繰り返し
        while ($tmp_day->lte($last_day)) {
            // 1ヶ月のカレンダーが6週で表示される場合は、6週目で処理がズレる場合があるので条件分岐する
            if ($count < 6) {
                // 当月は日付、前月または翌月の場合は空白を設定
                $same_month = $tmp_day->month == $this->carbon->month;
                $days[] = $same_month ? new CalendarWeekDay($tmp_day->copy()) : new CalendarWeekBlankDay($tmp_day->copy());
            } else {
                // カレンダー上の6週目の日付は30または31なので、30以上は日付を表示する
                $days[] = $tmp_day->day >= 30 ? new CalendarWeekDay($tmp_day->copy()): new CalendarWeekBlankDay($tmp_day->copy());
            }
            //翌日に移動
            $tmp_day->addDay(1);
        }

        return $days;
    }
}
