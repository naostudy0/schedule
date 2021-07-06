<?php

namespace App\Plan\OperationDatabase;

use Illuminate\Support\Facades\Crypt;
use App\Models\Plan;
use Auth;
use App\Share\ShareIdChange;

class PlanStore
{
    private $result;
    private $inputs;
    private $date_redirect;

    /**
     * 予定を登録し、結果を文字列に設定する
     * 
     * @param object
     */
    public function __construct($request)
    {
        $request_array = $request->all();

        $share_store = new ShareStore($request_array);
        $share_user_id = $share_store->getData();


        try{
            $this->inputs = new Plan;
            $this->inputs->user_id = Auth::id();
    
            $this->inputs->start_date = $request->start_date;
            $this->inputs->start_time = $request->start_time;
            $this->inputs->end_date = $request->end_date;
            $this->inputs->end_time = $request->end_time;
            $this->inputs->color = $request->color;
            $this->inputs->content = Crypt::encrypt($request->content);
            $this->inputs->detail = Crypt::encrypt($request->detail);
            $this->inputs->share_user_id = $share_user_id;

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