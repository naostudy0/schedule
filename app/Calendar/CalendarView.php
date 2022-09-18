<?php

namespace App\Calendar;

use Carbon\Carbon;

class CalendarView
{
    /**
     * @var \Carbon\Carbon
     */
    private $carbon;

    /**
     * Carbonをインスタンス化
     *
     * @param int
     */
    public function __construct($timestamp)
    {
        $this->carbon = new Carbon($timestamp);
    }

    /**
     * カレンダーのタイトルを返す
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->carbon->format('Y年n月');
    }

    /**
     * カレンダーを出力する
     *
     * @return string
     */
    public function render()
    {
        $html[] = <<<EOT
        <div class="calendar">
            <table class="table">
            <thead>
                <tr>
                <th>日</th>
                <th>月</th>
                <th>火</th>
                <th>水</th>
                <th>木</th>
                <th>金</th>
                <th>土</th>
                </tr>
            </thead>
            <tbody>
        EOT;

        $weeks = $this->getWeeks();

        $count = 0;
        foreach ($weeks as $week) {
            $html[] = '<tr class="'.$week->getClassName().'">';

            $count++;
            $days = $week->getDays($count);
            foreach($days as $day){
                $html[] = '<td class="'.$day->getClassName().'">';
                $html[] = $day->render();
                $html[] = '</td>';
            }
            $html[] = '</tr>';
        }

        $html[] = <<< EOT
            </tbody>
            </table>
        </div>
        EOT;

        return implode('', $html);
    }

    /**
     * 何週目かを返す
     *
     * @return array
     */
    private function getWeeks()
    {
        // 月初と月末の日付を設定
        $first_day = $this->carbon->copy()->firstOfMonth();
        $last_day = $this->carbon->copy()->lastOfMonth();

        // 作業用 月曜日を取得
        $tmp_day = $first_day->copy()->addDay(7)->startOfWeek();

        $index = 0;
        // 1週目 (1日が日曜日の場合、後にweeks[0]をgetDays()すると、7日間全て前月の日付となるため除外する)
        if (! $first_day->dayOfWeek == 0) {
            $week = new CalendarWeek($first_day->copy(), $index);
            $weeks[] = $week;
        }

        // 月末までループさせる
        while ($tmp_day->lte($last_day)) {
            //週カレンダーViewを作成する
            $index++;
            $week = new CalendarWeek($tmp_day, $index);
            $weeks[] = $week;

            //次の週の月曜日
            $tmp_day->addDay(7);
        }

        if ($last_day->dayOfWeek == 0) {
            $index++;
            $week = new CalendarWeek($tmp_day, $index);
            $weeks[] = $week;
        }

        return $weeks;
    }

    /**
     * 翌月
     *
     * @return string
     */
    public function getNextMonth()
    {
        return $this->carbon->copy()->addMonthNoOverflow()->format('Y-m');
    }

    /**
     * 前月
     *
     * @return string
     */
    public function getPreviousMonth()
    {
        return $this->carbon->copy()->subMonthNoOverflow()->format('Y-m');
    }
}
