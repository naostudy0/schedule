<?php

namespace App\Share;

use App\Share\ShareIdChange;
use App\Share\ShareIdExist;
use App\Share\OperationDatabase\SharePermitDelete;
use App\Models\User;
use Auth;

class ShareDelete
{
    private $result;
    private $msg;

    /**
     * 予定共有を解除する
     * 
     * 自分が相手に共有していた予定から相手のIDを削除
     * 自分の予定共有ユーザ情報から相手のIDを削除
     * 
     * 相手が自分に共有していた予定から自分のIDを削除
     * 相手の予定共有ユーザ情報から自分のIDを削除
     * 
     * @param string
     */
    public function __construct($share_id)
    {
        // 誤ったshare_idを送信された時の処理
        $share_id_exist = new ShareIdExist($share_id);
        $exist = $share_id_exist->getResult();
        if(!$exist){
            $this->result = false;
            $this->msg = '操作に失敗しました。';

        } else {
            $own_id = Auth::id();

            // 相手のshare_idをIDに変換して取得
            $share_id_change = new ShareIdChange($share_id);
            $fellow_id = $share_id_change->getId();

            // 自分の共有ユーザ情報から相手のIDを削除した文字列を取得
            $share_delete_own = new SharePermitDelete($own_id, $fellow_id);
            $own_update_data = $share_delete_own->getData();

            // 自分のIDを削除するためのキー
            $own_search = $share_delete_own->getKey();
    
            // 相手が自分に対して共有している予定を取得
            $own_update_plan = $share_delete_own->getPlan();

            // 相手の共有ユーザ情報から自分のIDを削除した文字列を取得
            $share_delete_fellow = new SharePermitDelete($fellow_id, $own_id);
            $fellow_update_data = $share_delete_fellow->getData();
    
            // 相手のIDを削除するためのキー
            $fellow_search = $share_delete_fellow->getKey();

            // 自分が相手に対して共有している予定を取得
            $fellow_update_plan = $share_delete_fellow->getPlan();


            try{
                // ------ 自分の情報 ------
                // 自分が相手に対して共有している予定から、相手のIDを削除して共有を解除
                foreach ($own_update_plan as $own_update){
                    $own_share_user_id = $own_update['share_user_id'];
                    $own_update_share_user_id = str_replace($own_search, '', $own_share_user_id);
        
                    // 相手のIDを削除することにより、誰とも共有しない予定となった場合はnull
                    if($own_update_share_user_id == ''){
                        $own_update_share_user_id = null;
                    } else {

                        // 相手のIDを削除することにより、IDをカンマで挟む文字列にならない場合はカンマを補う（ex 1,2,3, を ,1,2,3,）
                        if (substr($own_update_share_user_id, 0, 1) != ',') {
                            $own_update_share_user_id = ',' . $own_update_share_user_id;
                        } elseif (substr($own_update_share_user_id, -1, 1) != ',') {
                            $own_update_share_user_id = $own_update_share_user_id . ',';
                        }
                    }
        
                    // 自分の予定の共有情報を更新
                    $own_update->update([
                        'share_user_id' => $own_update_share_user_id,
                    ]);
                }
    
                // 自分の予定共有ユーザ情報を更新
                $own_user = User::where('id', $own_id)->first();
                $own_user->update([
                    'share_user_id' => $own_update_data,
                ]);


                // ------ 相手の情報 ------
                // 相手が自分に対して共有している予定から、自分のIDを削除して共有を解除
                foreach ($fellow_update_plan as $fellow_update){
                    $fellow_share_user_id = $fellow_update['share_user_id'];
                    $fellow_update_share_user_id = str_replace($fellow_search, '', $fellow_share_user_id);
        
                    // 自分のIDを削除することにより、誰とも共有しない予定となった場合はnull
                    if($fellow_update_share_user_id == ''){
                        $fellow_update_share_user_id = null;
        
                    } else {
                        // 自分のIDを削除することにより、IDをカンマで挟む文字列にならない場合はカンマを補う
                        if (substr($fellow_update_share_user_id, 0, 1) != ',') {
                            $fellow_update_share_user_id = ',' . $fellow_update_share_user_id;
                        } elseif (substr($fellow_update_share_user_id, -1, 1) != ',') {
                            $fellow_update_share_user_id = $fellow_update_share_user_id . ',';
                        }
                    }
        
                    // 相手の予定の共有情報を更新
                    $fellow_update->update([
                        'share_user_id' => $fellow_update_share_user_id,
                    ]);
                }

                // 相手の予定共有ユーザ情報を更新
                $fellow_user = User::where('id', $fellow_id)->first();
                $fellow_user->update([
                    'share_user_id' => $fellow_update_data,
                ]);

                $this->result = true;
                $this->msg = '解除しました。';
    
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