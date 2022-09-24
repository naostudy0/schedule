<?php

namespace App\Share;

use App\Models\User;

class ShareUserList
{
    private $share_users_id;

    /**
     * 予定共有しているユーザの情報を取得
     * （usersテーブルのshare_user_idは「,1,2,」のように共有しているユーザのIDの前後にカンマがつく形で保存している）
     * 
     * @param int
     */
    public function __construct($id)
    {
        $user = User::where('id', $id)->first();
        $share_users_id = $user['share_user_id'];

        // 予定共有ユーザがいない場合はnull
        if(!$share_users_id || $share_users_id == ''){
            $this->share_users_data = null;

        }else{
            // 先頭と最後の「,」を除外
            $len = strlen($share_users_id);
            $share_users_text = substr($share_users_id, 1, $len-2);

            // カンマ区切りになった文字列を配列に変更
            $this->share_users_id = explode(',', $share_users_text);

            // usersテーブルを参照し、IDからshare_id（ユーザ検索用ID）とnameを取得
            $count = 0;
            foreach ($this->share_users_id as $user_id){
                $user_data = User::where('id', $user_id)->first();
                $this->share_users_data[$count]['share_id'] = $user_data['share_id'];
                $this->share_users_data[$count]['name'] = $user_data['name'];

                $count++;
            }
        }
    }

    /**
     * 予定共有しているユーザのshare_id（ユーザ検索用ID）とnameを配列で返す
     * 
     * @return array|null
     */
    public function getData()
    {
        return $this->share_users_data;
    }

    /**
     * 予定共有しているユーザのIDを配列で返す
     * 
     * @return array|null
     */
    public function getId()
    {
        return $this->share_users_id;
    }
}