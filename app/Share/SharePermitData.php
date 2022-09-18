<?php

namespace App\Share;

use App\Models\User;

class SharePermitData
{
    private $share_user_text;

    /**
     * usersテーブルのshare_user_idに保存する文字列を作成
     * 
     * @param int $id
     * @param int $fellow_id
     */
    public function __construct($id, $fellow_id)
    {
        // 既に登録されているshare_user_idを取得
        $user = User::where('id', $id)->first();
        $share_user_id = $user['share_user_id'];

        if($share_user_id){
            // 先頭一文字を除外（ex 1,2,)
            $share_user_data = substr($share_user_id, 1);
        } else {
            $share_user_data = null;
        }


        // 既に予定共有しているユーザがいる場合 (ex ,1,2,3, )
        // 予定共有しているユーザがいない場合 (ex ,3, )
        $this->share_user_text = ',' . $share_user_data . $fellow_id . ',';
    }

    /**
     * usersテーブルのshare_user_idに保存する文字列を返す
     * 
     * @return string
     */
    public function getText()
    {
        return $this->share_user_text;
    }
}