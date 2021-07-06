<?php

namespace App\Share;

use Illuminate\Support\Facades\Validator;
use App\Share\ShareIdExist;
use App\User;
use Auth;

class ShareSearch
{
    private $result;
    private $user_data;
    private $msg;

    /**
     * share_id（ユーザ検索用ID）のユーザがユーザ検索表示を許可している場合に該当のユーザを表示する
     * 
     * @param object
     */
    public function __construct($request)
    {
        // share_idが1~8文字の英数字かどうか
        $validator = Validator::make($request->all(),[
            'share_id' => ['between:1,8', 'regex:/^[a-zA-Z0-9]+$/'],
        ]);

        if ($validator->fails()){
            $this->result = false;
            $this->msg = '8文字以内の英数字を入力してください。';

        } else {
            
            $share_id = $request->input('share_id');
            $share_id_exist = new ShareIdExist($share_id);
    
            // share_idが存在するか
            $exist = $share_id_exist->getResult();
    
            // 入力されたshare_idのユーザが予定共有を許可しているか
            $share_permission = $share_id_exist->getResultPermission();
            
            if($exist){
                $user = Auth::user();
                $own_id = $user->share_id;
    
                // 自分のshare_idの場合
                if ($share_id == $own_id){
    
                    $this->result = false;
                    $this->msg = 'あなたのIDです。';
    
                // 該当のユーザが予定共有を許可している場合
                } elseif($share_permission) {
                    $this->result = true;
                    $this->user_data = User::where('share_id', $share_id)->where('share_permission', 1)->first();
                
                } else {
                    $this->result = false;
                    $this->msg = '該当のユーザーが見つかりません。';
                }
    
            } else {
                $this->result = false;
                $this->msg = '該当のユーザーが見つかりません。';
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
     * 表示するメッセージを返す
     * 
     * @return string
     */
    public function getMessage()
    {
        return $this->msg;
    }

    /**
     * viewに渡すユーザ情報を返す
     * 
     * @return object
     */
    public function getData()
    {
        return $this->user_data;
    }
}