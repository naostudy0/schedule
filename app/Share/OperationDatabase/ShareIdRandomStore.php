<?php

namespace App\Share\OperationDatabase;

use App\Models\Plan;
use App\Share\ShareIdRandom;
use Auth;

class ShareIdRandomStore
{
    private $result;

    /**
     * 他と重複しない8文字のshare_idを作成しusersテーブルに保存
     * （アルファベット大文字小文字・数字」のランダムな文字列）
     * 
     */
    public function __construct()
    {
        $share_id_random = new ShareIdRandom;
        $result_random_make = $share_id_random->getResult();
        
        // ユニークな文字列が作成できた場合は更新
        if($result_random_make){
            try{
                $user = Auth::user();
                $random_str = $share_id_random->getData();
                $user->update(['share_id' => $random_str]);
                $this->result = true;

            } catch(\Exception $e) {
                $this->result = false;
            }

        } else {
            $this->result = false;
        }
    }

    /**
     * 結果を返す
     * 
     * @return bool
     */
    public function getResult()
    {
        return $this->result;
    }
}