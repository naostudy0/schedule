<?php

namespace App\Share;

use App\Models\ShareRequest;
use App\User;
use Auth;

class ShareRequested
{
    private $request_user_data;

    /**
     * share_id（ユーザ検索用ID）とnameを返す
     * 
     * $search_stringは'requested_user_id'か'user_id'のいずれか
     * （ShareRequestsテーブルのカラム名のため、それ以外はエラーとなる）
     * 
     * user_id:予定共有申請したユーザのID、requested_user_id:予定共有申請されたユーザのID
     * であるため、自分のIDをどちらに当てはめるかによって結果が異なる
     * 
     * user_id の場合、自分が予定共有申請中（未回答）のユーザのshare_idと名前を取得
     * requested_user_id の場合、自分へ予定共有申請中（未回答）のユーザのshare_idと名前を取得
     * 
     * @param string
     */
    public function __construct($search_string)
    {
        // 検索するカラム名を設定
        if ($search_string == 'requested_user_id'){
            $search_string_user = 'user_id';

        } elseif($search_string = 'user_id') {
            $search_string_user = 'requested_user_id';
        }

        $id = Auth::id();

        if (!ShareRequest::where($search_string, $id)->where('status', 0)->exists()){
            $this->request_user_data = null;

        } else {
            $request_users = ShareRequest::where($search_string, $id)->where('status', 0)->get()->toArray();

            // share_idとnameを取得
            $count = 0;
            foreach($request_users as $request_user){
                $user = User::where('id', $request_user[$search_string_user])->first();
                $this->request_user_data[$count]['share_id'] = $user['share_id'];
                $this->request_user_data[$count]['name'] = $user['name'];
                $count++;
            }
        }
    }
    
    /**
     * share_id（ユーザ検索用ID）と名前を配列で返す。
     * 
     * @return array|null
     */
    public function getData()
    {
        return $this->request_user_data;
    }
}