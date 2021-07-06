<?php

namespace App\Share;

use App\Share\OperationDatabase\ShareIdRandomStore;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use App\User;
use Auth;

class ChangeStatus
{
    private $msg;

    /**
     * ユーザ検索表示可否、またはshare_id（ユーザ検索用ID）を変更して結果を返す
     * 
     * @param object
     */
    public function __construct($request)
    {
        $user = Auth::user();

        // ボタンが3つ別れているため、いずれかの処理
        $share_permission = $request->input('share_permission');
        $share_id = $request->input('share_id');
        $random = $request->input('random');

        // share_idが送信された場合
        if ($share_id){
            $validator = Validator::make($request->all(),[
                'share_id' => ['required', 'max:8', 'unique:users,share_id', 'regex:/^[a-zA-Z0-9]+$/'],
            ]);

            if ($validator->fails()){
                $result = false;
                $result_msg = '8文字以内の英数字ではないか、または他のユーザーに使用されているIDです。';
            } else {
                try{
                    $user->update(['share_id' => $share_id]);
                    $result = true;
    
                } catch(\Exception $e) {
                    $result = false;
                }

            }

        // share_idを自動作成する場合
        } elseif($random){

            // ランダムな文字列を作成し、share_idを更新
            $share_id_random_store = new ShareIdRandomStore;
            $result = $share_id_random_store->getResult();
            

        // ユーザ検索表示可否の変更
        } elseif($share_permission){
            // 現在のステータスに応じて変更
            $share_permission = $user->share_permission;

            // 0:許可しない 1:許可する
            if($share_permission == 0){
                $permission = 1;
            } else {
                $permission = 0;
            }

            try{
                $user->update(['share_permission' => $permission]);
                $result = true;

            } catch(\Exception $e) {
                $result = false;
            }

        // もしも上記3つのデータが無かった場合
        }else{
            $result = false;
        }

        // 結果に応じてメッセージ作成
        if ($result) {
            $this->msg = 'ステータスを変更しました。';
        } elseif($result_msg) {
            $this->msg = $result_msg;
        } else {
            $this->msg = 'ステータスの更新に失敗しました。';
        }
    }

    /**
     * 結果のメッセージを返す
     * 
     * @return string
     */
    public function getMessage()
    {
        return $this->msg;
    }
}