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

        $this->share['msg'] = $user->share_permission ? '許可しています' : '許可していません';
        $this->share['btn'] = $user->share_permission ? '許可しない' : '許可する';
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