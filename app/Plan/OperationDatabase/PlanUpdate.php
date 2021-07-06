<?php

namespace App\Plan\OperationDatabase;

use Illuminate\Support\Facades\Crypt;
use App\Models\Plan;
use Auth;

class PlanUpdate
{
    private $result;
    private $update_data;
    private $plan;
    private $id;
    private $date_redirect;

    /**
     * 該当のidの予定を更新し、結果を文字列に設定する
     * 
     * @param object
     */
    public function __construct($request)
    {
        $this->update_data = $request->all();

        $share_store = new ShareStore($this->update_data);
        $this->update_data['share_user_id'] = $share_store->getData();

        $this->id = Crypt::decrypt($this->update_data['id']);
        try {
            $this->plan = new Plan;

            $this->plan->where('id', $this->id)->update([
                'start_date' => $this->update_data['start_date'],
                'start_time' => $this->update_data['start_time'],
                'end_date' => $this->update_data['end_date'],
                'end_time' => $this->update_data['end_time'],
                'color' => $this->update_data['color'],
                'content' => Crypt::encrypt($this->update_data['content']),
                'detail' => Crypt::encrypt($this->update_data['detail']),
                'share_user_id' => $this->update_data['share_user_id'],
            ]);
            
            $this->result = '予定を更新しました。';

            // リダイレクト先のURLを作成
            $this->date_redirect = '/schedule?date=' . substr($this->update_data['start_date'], 0, 7);

        } catch(\Exception $e) {
            $this->result = '予定の更新に失敗しました。';

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