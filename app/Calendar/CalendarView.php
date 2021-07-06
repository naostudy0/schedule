<?php

namespace App\Calendar;

use Carbon\Carbon;

class CalendarView
{
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

    private $html;
    protected $count = 0;

    /**
     * カレンダーを出力する
     * 
     * @return string
     */
    public function render()
    {
        $this->html[] = <<<EOT
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

        foreach($weeks as $week){
            $this->html[] = '<tr class="'.$week->getClassName().'">';

            $this->count++;
            $days = $week->getDays($this->count);
            foreach($days as $day){
                $this->html[] = '<td class="'.$day->getClassName().'">';
                $this->html[] = $day->render();
                $this->html[] = '</td>';
            }
            $this->html[] = '</tr>';
        }
    
        $this->html[] = <<< EOT
            </tbody>
            </table>
        </div>
        EOT;

        return implode("", $this->html);
    }

    protected $weeks;
    protected $first_day;
    protected $last_day;
    protected $tmp_day;
    protected $week;
    protected $index = 1;

    /**
     * 何週目かを返す
     * 
     * @return array
     */
    protected function getWeeks()
    {

        // 月初と月末の日付を設定
        $this->first_day = $this->carbon->copy()->firstOfMonth();
        $this->last_day = $this->carbon->copy()->lastOfMonth();

        // 作業用 月曜日を取得
        $this->tmp_day = $this->first_day->copy()->addDay(7)->startOfWeek();

        // 1週目 (1日が日曜日の場合、後にweeks[0]をgetDays()すると、7日間全て前月の日付となるため除外する)
        if (!$this->first_day->dayOfWeek == 0){
            $this->week = new CalendarWeek($this->first_day->copy(), $this->index);
            $this->weeks[] = $this->week;
        } else {
            $this->index = 0;
        }

        // 月末までループさせる
        while($this->tmp_day->lte($this->last_day)){
            //週カレンダーViewを作成する
            $this->index++;
            $this->week = new CalendarWeek($this->tmp_day, $this->index);
            $this->weeks[] = $this->week;
            
            //次の週の月曜日
            $this->tmp_day->addDay(7);
        }

        if ($this->last_day->dayOfWeek == 0) {
            $this->index++;
            $this->week = new CalendarWeek($this->tmp_day, $this->index);
            $this->weeks[] = $this->week;
        }

        return $this->weeks;
    }

    /**
     * 翌月
     * 
     */
    public function getNextMonth()
    {
      return $this->carbon->copy()->addMonthNoOverflow()->format('Y-m');
    }

    /**
     * 前月
     * 
     */
    public function getPreviousMonth()
    {
      return $this->carbon->copy()->subMonthNoOverflow()->format('Y-m');
    }
}