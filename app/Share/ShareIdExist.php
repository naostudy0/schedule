<?php

namespace App\Share;

use App\User;

class ShareIdExist
{
    private $share_id_exist;
    private $user_exists;

    /**
     * 存在するshare_id（ユーザ検索用ID）か確認する
     * 存在する場合はレコードを取得する
     * 
     * ユーザ検索を許可しているかどうかを確認する
     * 
     * @param int
     */
    public function __construct($share_id)
    {
        $this->share_id_exist = User::where('share_id', $share_id)->exists();

        // 該当のshare_idのユーザが共有を許可しているか確認
        $this->user_permission = User::where('share_id', $share_id)->where('share_permission', 1)->exists();
    }

    /**
     * share_idが存在するかどうか結果を返す
     * 
     * @return bool
     */
    public function getResult()
    {
        return $this->share_id_exist;
    }

    /**
     * ユーザ検索を許可しているか結果を返す
     * 
     * @return bool
     */
    public function getResultPermission()
    {
        return $this->user_permission;
    }
}