<?php

namespace App\Share;

use App\Models\ShareRequest;
use App\User;
use Auth;

class ShareSendDelete
{
    /**
     * 自分が出した共有申請を取り消す
     * 
     * @param int $id
     * @param string $share_id
     */
    public function __construct($id, $share_id)
    {
        $user = new User;
        $requested_user_id = $user->where('share_id', $share_id)->first();

        $exist = ShareRequest::where('user_id', $id)->where('requested_user_id', $requested_user_id->id)->exists();
        
        if (!$exist) {

            $this->result = false;
            $this->msg = '操作に失敗しました。';

        } else {
            try{
                ShareRequest::where('user_id', $id)->where('requested_user_id', $requested_user_id->id)->delete();

                $this->result = true;
                $this->msg = '申請を取り消しました。';

            } catch(\Exception $e) {

                $this->result = false;
                $this->msg = '操作に失敗しました。';
            }
        }
    }

    /**
     * 画面に表示するメッセージを返す
     * 
     * @return string
     */
    public function getMessage()
    {
        return $this->msg;
    }
}