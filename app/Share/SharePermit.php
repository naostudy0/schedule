<?php

namespace App\Share;

use Illuminate\Support\Facades\Validator;
use App\Share\OperationDatabase\ShareReqeustExist;
use App\Share\OperationDatabase\SharePermitStore;
use App\Models\User;
use Auth;

class SharePermit
{
    /**
     * 自分への予定共有申請を許可または拒否する
     * 
     * @param object
     */
    public function __construct($request){
        $share_id = $request->input('share_id');
        $permit = $request->input('permit');
    
        // 値が1（許可）、2（拒否）か確認
        $validator = Validator::make($request->all(),[
            'permit' => ['digits:1', 'regex:/^[1-2]+$/'],
        ]);
        
        // 自分への予定共有依頼が存在するか確認
        $share_request_exist = new ShareReqeustExist($share_id);
        $exist = $share_request_exist->getExist();
    
        // 自分への予定共有依頼が存在しない場合、または誤ったpermitを送信された場合
        if (!$exist || $validator->fails()) {
            $this->result = false;
            $this->msg = '操作に失敗しました。';
    
        // 問題ない場合
        } else {
            $permit = $request->input('permit');
            $share_permit_store = new SharePermitStore($permit, $share_request_exist);
            $this->result = $share_permit_store->getResult();
            $this->msg = $share_permit_store->getMessage();
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