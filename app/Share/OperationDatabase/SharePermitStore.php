<?php

namespace App\Share\OperationDatabase;

use App\Models\ShareRequest;
use App\Share\SharePermitData;
use App\Models\User;
use Auth;

class SharePermitStore
{
    private $result;
    private $msg;

    /**
     * usersテーブルのshare_user_idに文字列を保存する
     * 
     * @param int $permit
     * @param object $share_request_exist
     */
    public function __construct($permit, $share_request_exist)
    {
       // 許可の処理
        if ($permit == 1){

            try {
                $user = Auth::user();

                // 相手のIDを取得
                $fellow_id = $share_request_exist->getUserId();

                // 自分のshare_user_idカラム更新用の文字列を取得
                $share_permit_data_own = new SharePermitData($user['id'], $fellow_id);
                $share_user_id_own = $share_permit_data_own->getText();

                // 自分のレコードを更新
                $user->update(['share_user_id' => $share_user_id_own]);


                $fellow_user = User::where('id', $fellow_id)->first();

                // 相手のshare_user_idカラム更新用の文字列を取得
                $share_permit_data_fellow = new SharePermitData($fellow_id, $user['id']);
                $share_user_id_fellow = $share_permit_data_fellow->getText();

                // 相手のレコードを更新
                $fellow_user->update(['share_user_id' => $share_user_id_fellow,]);

                // ShareRequestsテーブルのIDを取得
                $record_id = $share_request_exist->getID();

                // 該当レコードのステータスを「1:許可」に更新
                $share_request = new ShareRequest;
                $share_request->where('id', $record_id)->update([
                    'status' => $permit,
                ]);
                
                $this->result = true;
                $this->msg = '共有を許可しました。';


            } catch(\Exception $e) {

                $this->result = true;
                $this->msg = '操作に失敗しました。';
            }

        // 拒否の処理
        } else {

            try {
                // ShareRequestsテーブルのIDを取得
                $record_id = $share_request_exist->getID();

                // 該当レコードのステータスを「2:拒否」に更新
                $share_request = new ShareRequest;
                $share_request->where('id', $record_id)->update([
                    'status' => $permit,
                ]);


                $this->result = true;
                $this->msg = '共有を拒否しました。';

            } catch(\Exception $e) {

                $this->result = true;
                $this->msg = '操作に失敗しました。';
            }
        }
    }

    /**
     * 処理の結果を返す
     * 
     * @return bool
     */
    public function getResult()
    {
        return $this->result;
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