<?php

namespace App\Plan;

class PlanCreate
{
    private array $initialize;
    private string $year;
    private string $month;
    private string $day;
    private string $cancel_check;

    /**
     * views.plan.create用の変数を初期化する
     * 
     * クエリパラメータ(date)がある場合は正しい日付かどうかを判断し
     * 正しければ初期値として設定する
     * 
     * @param object
     */
    public function __construct($request)
    {
        $this->initialize['start_date'] = '';
        $this->initialize['start_time'] = '00:00';
        $this->initialize['end_date'] = '';
        $this->initialize['end_time'] = '00:00';
        $this->initialize['content'] = '';
        $this->initialize['detail'] = '';
        $this->initialize['cancel_date'] = '';

        // クエリパラメータのチェック
        $date = $request->input("date");

        // 存在している日付か確認するために分割
        if($date)
        {
            list($this->year, $this->month, $this->day) = explode('-', $date);

            // 予定入力前に表示していた月に戻れるように「キャンセル」用のdate作成
            $this->cancel_check = $this->year . '-' . $this->month;
            
            // 年月のデータが正しければ変数を更新
            if($date && preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $date) && checkdate($this->month, $this->day, $this->year)){

                $this->initialize['start_date'] = $date;
                $this->initialize['end_date'] = $date;
                $this->initialize['cancel_date'] = $this->cancel_check;
            }
        }
    }

    /**
     * views.plan.create用の初期値を返す
     * 
     * @return array
     */
    public function getInitialize()
    {
        return $this->initialize;
    }
}