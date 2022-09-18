<?php

namespace App\Share;

use Illuminate\Support\Facades\Crypt;
use App\Models\User;

class ShareIdRandom
{
    private $random_str;
    private $result;

    /**
     * 他と重複しない8文字のshare_idを作成（アルファベット大文字小文字・数字」のランダムな文字列）
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
            $this->random_str = substr($random, $index, 8);

            // ユニークな文字列が作成できた場合は終了
            if(!User::where('share_id', $this->random_str)->exists()){
                $this->result = true;
                break;

            } else {
                // max_countまで作成できなかった場合はfalse
                $count = $max_count ? $this->result = false : $count++;
            }
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

    /**
     * share_idとして使用できる文字列を返す
     * 
     * @return string
     */
    public function getData()
    {
        return $this->random_str;
    }
}