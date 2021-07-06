<?php

namespace App\Plan\OperationDatabase;

use App\Share\ShareIdNameGet;
use App\User;
use Auth;

class PlanShare
{
    private $share_user_id;
    private $share_users;

    /**
     * 自分が予定共有しているユーザのshare_id（ユーザ検索用ID）とnameを取得する
     * 
     */
    public function __construct()
    {
        $user = Auth::user();
        $share_user_data = $user['share_user_id'];

        if(!$share_user_data){
            $this->share_users = null;
        }else{
            // share_idとnameを配列で取得
            $share_id_name_get = new ShareIdNameGet($share_user_data);
            $this->share_users = $share_id_name_get->getData();
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
}