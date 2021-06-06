<?php

namespace App\Plan\OperationDatabase;

use App\Models\Plan;
use Auth;

class PlanDelete
{
    private string $result;
    private string $date_redirect;
    private object $plan;
    private array $record;
    private object $plan_exist;
    private int $delete_id;

    /**
     * 該当のidの予定を削除し、結果を文字列に設定する
     * 
     * @param object
     */
    public function __construct($request)
    {
        try{
            $this->plan = new Plan;

            // リダイレクト先のURLを作成
            $this->plan_exist = new PlanExist($request);
            $this->record = $this->plan_exist->getRecord();
            $this->date_redirect = '/schedule?date=' . substr($this->record['start_date'], 0, 7);
            
            $this->delete_id = decrypt($this->record['id']);
            $this->plan->where('id', $this->delete_id)->delete();
            $this->result = '予定を削除しました。';

        } catch(\Exception $e) {
            // リダイレクト先のURLを作成
            $this->date_redirect = '/schedule';
            $this->result = '削除に失敗しました。';
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