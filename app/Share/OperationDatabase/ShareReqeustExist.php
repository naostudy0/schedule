<?php

namespace App\Share\OperationDatabase;

use App\Models\ShareRequest;
use App\Models\User;
use Auth;

class ShareReqeustExist
{
    private $exist;
    private $record_id;
    private $record_user_id;
    private $own_id;

    /**
     * 自分への予定共有依頼、または自分が申請した予定共有依頼が存在するか確認する
     * 
     * @param string
     */
    public function __construct($share_id)
    {
        // 誤ったshare_id（ユーザ検索用ID）を送られた時の対策
        if (User::where('share_id', $share_id)->exists()){

            // 自分へ共有申請したユーザのID
            $user = User::where('share_id', $share_id)->first();
            $user_id = $user->id;

        } else {
            $user_id = '';
        }

        // 自分のID
        $this->own_id = Auth::id();

        // 自分への予定共有依頼が存在するか確認（status:0は申請後、未回答状態）
        $this->exist = ShareRequest::where('requested_user_id', $this->own_id)->where('user_id', $user_id)->where('status', 0)->exists();

        // 存在する場合は該当レコードのIDと相手のIDを取得
        if($this->exist){
            $record = ShareRequest::where('requested_user_id', $this->own_id)->where('user_id', $user_id)->where('status', 0)->first();
            $this->record_id = $record->id;
            $this->record_user_id = $record->user_id;
        }
    }

    /**
     * 自分への予定共有依頼が存在するかを返す
     * 
     * @return bool
     */
    public function getExist()
    {
        return $this->exist;
    }

    /**
     * 該当レコードのidを返す
     * 
     * @return int
     */
    public function getID()
    {
        return $this->record_id;
    }

    /**
     * 自分へ申請をしているユーザーのidを返す
     * 
     * @return int
     */
    public function getUserId()
    {
        return $this->record_user_id;
    }
}
