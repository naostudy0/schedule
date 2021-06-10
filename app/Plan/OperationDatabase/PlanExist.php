<?php

namespace App\Plan\OperationDatabase;

use Illuminate\Support\Facades\Crypt;
use App\Models\Plan;
use Auth;

class PlanExist
{
    private array $request_all;
    private string $plan_id;
    private int $user_id;
    private bool $exist;
    private object $record;

    /**
     * 予定の更新や削除の時に送信された予定のidが、ログインしているユーザが登録したデータかどうかを判定する
     * 
     * @param object
     */
    public function __construct($request)
    {
        $this->request_all = $request->all();
        // 複合化
        $this->plan_id = Crypt::decrypt($this->request_all['id']);

        $this->user_id = Auth::id();
        $this->exist = Plan::where('id', $this->plan_id)->where('user_id', $this->user_id)->exists();

        if($this->exist) $this->record = Plan::where('id', $this->plan_id)->where('user_id', $this->user_id)->first();
    }

    /**
     * 結果を変数で返す
     * 
     * @return bool
     */
    public function getResult()
    {
        return $this->exist;
    }

    protected array $record_array;

    /**
     * idを再度暗号化、content、detailを復号化してレコードを配列で返す
     * 
     * @return array
     */
    public function getRecord()
    {
        $this->record_array = $this->record->toArray();

        $this->record_array['id'] = Crypt::encrypt($this->record_array['id']);
        $this->record_array['content'] = Crypt::decrypt($this->record_array['content']);
        if($this->record_array['detail']){
            $this->record_array['detail'] = Crypt::decrypt($this->record_array['detail']);
        }

        return $this->record_array;
    }
}