<?php

namespace App\Share\OperationDatabase;

use App\Models\Plan;
use App\Share\ShareUserList;

class SharePermitDelete
{
    private $own_update_data;
    private $search;
    private $plan;

    /**
     * 第一引数のユーザから、第二引数のユーザの共有を解除するための情報を取得する
     * 
     * @param int $own_id
     * @param int $fellow_id
     */
    public function __construct($own_id, $fellow_id)
    {
        $share_user_list = new ShareUserList($own_id);

        // 予定共有しているユーザーのIDを配列で取得
        $own_data = $share_user_list->getId();

        // 取得した配列から自分の共有ユーザーのIDを削除
        $own_key = array_search($fellow_id, $own_data);
        unset($own_data[$own_key]);

        // 配列をDBに保存するため、「,1,2,」のようにIDの前後にカンマがつく形の文字列にする
        // 自分を削除したことで、共有ユーザが0になった場合はimplodeでエラーとなるため条件分岐
        if ($own_data == null) {
            $this->own_update_data = null;
        } else {
            $this->own_update_data = ',' . implode(',', $own_data) . ',';
        }

        // 自分と共有されている予定を検索・削除するための文字列
        $this->search = ',' . $own_id . ',';

        // 共有を解除するユーザが自分と共有している予定
        $this->plan = Plan::where('user_id', $fellow_id)->where('share_user_id', 'like', "%$this->search%")->get();
    }

    /**
     * DB保存用の文字列を取得する（「,1,2,」のようにIDの前後にカンマがつく形の文字列）
     * 
     * @return string|null
     */
    public function getData()
    {
        return $this->own_update_data;
    }

    /**
     * 自分と共有されている予定を検索・削除するための文字列を返す
     * 
     * @return string
     */
    public function getKey()
    {
        return $this->search;
    }

    /**
     * 共有を解除するユーザーが自分と共有している予定を返す
     * 
     * @return object
     */
    public function getPlan()
    {
        return $this->plan;
    }
}