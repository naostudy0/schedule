<?php

namespace App\Plan\OperationDatabase;

use App\Share\ShareIdChange;

class ShareStore
{
    private $share_user_id;

    /**
     * 配列にshare_userが含まれている場合、DB保存用の文字列を作成する
     * 
     * @param array
     */
    public function __construct($data)
    {
        $exist = array_key_exists('share_user', $data);
        
        if(!$exist){
            $this->share_user_id = null;
            
        } else {
            $count = 0;
            foreach ($data['share_user'] as $share_user){
                $share_id_change = new ShareIdChange($share_user);
                $share_users_list[$count] = $share_id_change->getId();
                
                $count++;
            }

            $this->share_user_id = ',' . implode(',', $share_users_list) . ',';
        }
    }

    /**
     * DB保存用の文字列を返す
     * 
     * @return string|null
     */
    public function getData()
    {
        return $this->share_user_id;
    }
}