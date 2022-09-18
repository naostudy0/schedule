<?php

namespace App\Share;

use App\Models\User;

class ShareIdNameGet
{
    private $share_users;
    private $name;

    /**
     * カンマ区切りの文字列であるshare_user_idを、share_idとnameの配列に変換する
     * 
     * @param string
     */
    public function __construct($share_user_id)
    {
        // 先頭と最後の「,」を除外
        $len = strlen($share_user_id);
        $share_users_text = substr($share_user_id, 1, $len-2);

        // カンマ区切りになった文字列を配列に変更
        $users_id = explode(',', $share_users_text);

        $count = 0;
        foreach($users_id as $user_id){
            $user = User::where('id', $user_id)->first();
            $this->share_users[$count]['name'] = $user['name'];
            $this->share_users[$count]['share_id'] = $user['share_id'];

            $count++;
        }

        // 名前のみ
        foreach($users_id as $user_id){
            $user = User::where('id', $user_id)->first();
            $this->name['name'] = $user['name'];
        }
    }

    /**
     * share_idとnameを配列で返す
     * 
     * @return array
     */
    public function getData()
    {
        return $this->share_users;
    }

    /**
     * nameを配列で返す
     * 
     * @return array
     */
    public function getName()
    {
        return $this->name;
    }
}