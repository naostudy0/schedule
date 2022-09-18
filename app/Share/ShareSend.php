<?php

namespace App\Share;

use App\Share\ShareIdExist;
use App\Share\OperationDatabase\ShareReqeustExist;
use App\Models\ShareRequest;
use App\Models\User;
use Auth;

class ShareSend
{
    private $result;
    private $msg;

    /**
     * 予定共有の申請をする
     * 
     * @param string
     */
    public function __construct($share_id)
    {
        $user = new User;
        $requested_user_id = $user->where('share_id', $share_id)->first();

        $own_id = Auth::id();

        // 既に申請依頼をしていないか確認
        $exist = ShareRequest::where('user_id', $own_id)->where('requested_user_id', $requested_user_id->id)->where('status', 0)->exists();

        // 該当のユーザから申請が届いていないか確認
        $exist_own_send = ShareRequest::where('requested_user_id', $own_id)->where('user_id', $requested_user_id->id)->where('status', 0)->exists();

        // 既に予定共有していないか確認
        $user = Auth::user();
        $search = ',' . $requested_user_id['id'] . ',';
        $result = strpos($user['share_user_id'], $search);

        // 自分がshare_idを未登録でないか確認
        $share_id_own = $user['share_id'];

        // 既に申請依頼をしている場合
        if ($exist) {
            
            $this->result = false;
            $this->msg = '既に申請済みのユーザーです。';

        // 該当のユーザから申請が届いている場合
        } elseif($exist_own_send) {

            $this->result = false;
            $this->msg = '該当のユーザーから申請が届いています。';

        // 既に共有登録されている場合
        } elseif($result) {

            $this->result = false;
            $this->msg = '既に予定共有しています。';

        // 自分のshare_iが未登録の場合
        } elseif($share_id_own === '') {
            
            $this->result = false;
            $this->msg = '自分のユーザーIDを設定してください。';
            
        // 問題ない場合
        } else {

            try {
                $inputs = new ShareRequest;
                $inputs->user_id = $own_id;
                $inputs->requested_user_id = $requested_user_id->id;
                
                $inputs->save();

                $this->result = true;
                $this->msg = '予定の共有申請をしました。';
    
            } catch(\Exception $e) {

                $this->result = false;
                $this->msg = '申請に失敗しました。再度お試しください。';
            }
        }
    }

    /**
     * 画面表示するメッセージを取得する
     * 
     * @return string
     */
    public function getMessage()
    {
        return $this->msg;
    }
}