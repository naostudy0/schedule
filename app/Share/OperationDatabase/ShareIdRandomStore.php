<?php

namespace App\Share\OperationDatabase;

use Illuminate\Support\Facades\Crypt;
use App\Models\User;
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
        // 最大10回まで繰り返し
        $count = 0;
        $max_count = 10;
        while($count <= $max_count){

            // アルファベット大文字小文字、数字のランダムな8文字を作成
            $index = rand(0, 20);
            $random = Crypt::encrypt(rand(0,99999));
            $random_str = substr($random, $index, 8);

            // ユニークな文字列が作成できた場合は終了
            if(!User::where('share_id', $random_str)->exists()){
                break;
            }
        }

        // ユニークな文字列が作成できた場合は更新
        if($random_str){
            try{
                $user = Auth::user();
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