<?php

namespace App\Share;

use App\Share\OperationDatabase\ShareIdRandomStore;
use Auth;

class ShareShow
{
    /**
     * 自身のshare_id（ユーザ検索用ID）と、ユーザ検索画面の表示可否を確認
     * 
     */
    public function __construct()
    {
        $user = Auth::user();

        // share_id（未設定の場合は未設定という文字列）
        $this->share['id'] = $user->share_id;

        if(!$this->share['id'] || $this->share['id'] == '') {
            // ランダムな文字列を作成し、share_idを更新
            $share_id_random_store = new ShareIdRandomStore;
            $result = $share_id_random_store->getResult();

            $result ? $this->share['id'] = $user->share_id : $this->share['id'] = '未設定';
        }


        // ユーザ検索の検索結果に表示されるかどうか
        $share_permission = $user->share_permission;
        // 0:許可しない
        if($share_permission == 0) {
            $this->share['msg'] = '許可していません';
            $this->share['btn'] = '許可する';
        // 1:許可する
        } else {
            $this->share['msg'] = '許可しています';
            $this->share['btn'] = '許可しない';
        }
    }

    /**
     * ステータスを配列で返す
     * 
     * @return array
     */
    public function getData()
    {
        return $this->share;
    }
}