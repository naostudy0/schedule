<?php

namespace App\Plan\OperationDatabase;

use App\Models\Plan;
use Auth;

class PlanStore
{
    private string $result;
    private object $inputs;
    private string $date_redirect;

    /**
     * 予定を登録し、結果を文字列に設定する
     * 
     * @param object
     */
    public function __construct($request)
    {
        try{
            $this->inputs = new Plan;
            $this->inputs->user_id = Auth::id();
    
            $this->inputs->start_date = $request->start_date;
            $this->inputs->start_time = $request->start_time;
            $this->inputs->end_date = $request->end_date;
            $this->inputs->end_time = $request->end_time;
            $this->inputs->color = $request->color;
            $this->inputs->content = $request->content;
            $this->inputs->detail = $request->detail;
    
            $this->inputs->save();
            $this->result = '予定を登録しました。';

            // リダイレクト先のURLを作成
            $this->date_redirect = '/schedule?date=' . substr($request->start_date, 0, 7);

        } catch(\Exception $e) {
            $this->result = '登録に失敗しました。';

            // リダイレクト先のURLを作成
            $this->date_redirect = '/schedule';
        }
    }

    /**
     * 結果を文字列で返す
     * 
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * リダイレクト先のURLを返す
     * 
     * @return string
     */
    public function getRedirectDate()
    {
        return $this->date_redirect;
    }
}