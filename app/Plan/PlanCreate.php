<?php

namespace App\Plan;

use App\Plan\OperationDatabase\PlanShare;

class PlanCreate
{
    /**
     * views.plan.create用の変数を初期化する
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function getInitialize($request)
    {
        $initialize = [
            'start_date'  => '',
            'start_time'  => '00:00',
            'end_date'    => '',
            'end_time'    => '00:00',
            'content'     => '',
            'detail'      => '',
            'cancel_date' => '',
        ];

        // クエリパラメータのチェック
        $date = $request->input('date');

        // 存在している日付か確認するために分割
        if ($date) {
            list($year, $month, $day) = explode('-', $date);
            // 予定入力前に表示していた月に戻れるように「キャンセル」用のdate作成
            $cancel_check = $year . '-' . $month;

            // 年月のデータが正しければ変数を更新
            if ($date && preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $date) && checkdate($month, $day, $year)) {
                $initialize['start_date']  = $date;
                $initialize['end_date']    = $date;
                $initialize['cancel_date'] = $cancel_check;
            }
        }

        $plan_share = new PlanShare;
        $initialize['share_users'] = $plan_share->getData();

        return $initialize;
    }
}
