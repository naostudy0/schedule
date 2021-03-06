<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PlanRequest;
use App\Models\Plan;
use App\Plan\PlanCreate;
use App\Plan\PlanReCreate;
use App\Plan\PlanColor;
use App\Plan\LayoutSet\LayoutSet;
use App\Plan\OperationDatabase\PlanUpdate;
use App\Plan\OperationDatabase\PlanStore;
use App\Plan\OperationDatabase\PlanExist;
use App\Plan\OperationDatabase\PlanDelete;
use App\Plan\OperationDatabase\PlanShare;
use App\Share\ShareIdChange;
use App\Share\ShareIdNameGet;
use App\User;
use Auth;

class PlanController extends Controller
{
    /**
     * 「予定一覧」画面で「カレンダー」をクリック時
     * 「予定入力」画面を表示する
     * 
     */
    public function planCreate (Request $request) 
    {
        $plan_create = new PlanCreate($request);
        $plan_data = $plan_create->getInitialize();

        $plan_color = new PlanColor;
        $color_checked = $plan_color->getInitialize();

        return view('plan.create',[
            'plan_data' => $plan_data,
            'color_checked' => $color_checked,
        ]);
    }

    /**
     * 「予定入力」画面で「確認」をクリック時
     * 「登録内容確認」画面を表示する
     * 
     */
    public function planConfirm (PlanRequest $request) 
    {
        $plan_data = $request->all();

        // 予定共有するにチェックされているが、共有するユーザーが指定されていない場合は共有しないに変更
        $plan_data['share_user'] ?? $plan_data['share_users'] = 0;

        if ($plan_data['share_users'] == 1){
            foreach($plan_data['share_user'] as $share_id){
                $share_id_change = new ShareIdChange($share_id);
                $share_user_name[] = $share_id_change->getName();
            }
        } else {
            $share_user_name = null;
        }

        $layout_set = new LayoutSet($plan_data);

        return view('plan.create_confirm', [
            'plan_data' => $plan_data,
            'layout_set' => $layout_set,
            'share_user_name' => $share_user_name,
            ]);
    }

    /**
     * 「登録内容確認」画面で「修正」をクリック時
     * 「予定入力」を再表示する
     * 
     */
    public function planReCreate (Request $request) 
    {
        $plan_recreate = new PlanReCreate($request);
        $plan_data = $plan_recreate->getData();
        $plan_share = new PlanShare;
        $plan_data['share_users'] = $plan_share->getData();
        
        $color_checked = $plan_recreate->getColor();

        return view('plan.create',[
            'plan_data' => $plan_data,
            'color_checked' => $color_checked,
        ]);
    }

    /**
     * 「登録内容確認」画面で「登録」をクリック時
     * 入力した内容をPlanテーブルに保存し、「予定一覧」画面にリダイレクトする
     * 
     */
    public function planStore (PlanRequest $request) 
    {
        $plan_store = new PlanStore($request);
        $result = $plan_store->getResult();
        $date_redirect = $plan_store->getRedirectDate();


        \Session::flash('flash_msg', $result);
        return redirect()->to($date_redirect);
    }


    private $not_exist = '該当の予定が見つかりませんでした。';

    /**
     * 「予定一覧」画面で修正をクリック時
     * 「予定修正」画面を表示する
     * 
     */
    public function planUpdate (Request $request)
    {
        $plan_exist = new PlanExist($request);
        $exist = $plan_exist->getResult();

        if(!$exist){
            
            \Session::flash('flash_msg', $this->not_exist);
            return redirect()->route('schedule');

        } else {

            $plan_data = $plan_exist->getRecord();

            $plan_share = new PlanShare;
            $plan_data['share_users'] = $plan_share->getData();

            $plan_color = new PlanColor;
            $color_checked = $plan_color->getRedisplayData($plan_data['color']);

            return view('plan.update', [
                'plan_data' => $plan_data,
                'color_checked' => $color_checked,
                ]);
        }
    }

    /**
     * 「予定修正」画面で「確認」をクリック時
     * 「修正内容確認」画面を表示する
     * 
     */
    public function updateConfirm (PlanRequest $request)
    {
        $plan_data = $request->toArray();
        $layout_set = new LayoutSet($plan_data);

        $exist = array_key_exists('share_user', $plan_data);
        if ($exist) {
            foreach($plan_data['share_user'] as $share_id){
                $share_id_change = new ShareIdChange($share_id);
                $share_user_name[] = $share_id_change->getName();
            }
        } else {
            $share_user_name = null;
        }

        return view('plan.update_confirm', [
            'plan_data' => $plan_data,
            'layout_set' => $layout_set,
            'share_user_name' => $share_user_name,
            ]);
    }

    /**
     * 「修正内容確認」画面で「戻る」をクリック時
     * 「予定修正」画面を再表示する

     */
    public function planReUpdate (Request $request)
    {
        $plan_recreate = new PlanReCreate($request);
        $plan_data = $plan_recreate->getData();
        $plan_share = new PlanShare;
        $plan_data['share_users'] = $plan_share->getData();
        $color_checked = $plan_recreate->getColor();

        return view('plan.update',[
            'plan_data' => $plan_data,
            'color_checked' => $color_checked,
        ]);
    }

    /**
     * 「修正内容確認」画面で「登録」をクリック時
     * 入力した内容をPlanテーブルの該当のレコードに上書きし、「予定一覧」画面にリダイレクトする
     * 
     */
    public function updateStore(PlanRequest $request)
    {
        $plan_update = new PlanUpdate($request);
        $result = $plan_update->getResult();
        $date_redirect = $plan_update->getRedirectDate();

        \Session::flash('flash_msg', $result);
        return redirect()->to($date_redirect);
    }

    /**
     * 「予定一覧」画面で「削除」をクリック時
     * 「削除内容確認」画面を表示する
     * 
     */
    public function deleteConfirm(Request $request)
    {
        $plan_exist = new PlanExist($request);
        $exist = $plan_exist->getResult();

        if(!$exist){
            
            \Session::flash('flash_msg', $this->not_exist);
            return redirect()->route('schedule');

        } else {

            $plan_data = $plan_exist->getRecord();
            $layout_set = new LayoutSet($plan_data);

            if ($plan_data['share_user_id']){
                $plan_data['share_users'] = 1;
                $share_id_name_get = new ShareIdNameGet($plan_data['share_user_id']);
                $share_user_name = $share_id_name_get->getName();

            } else {
                $plan_data['share_users'] = 0;
                $share_user_name = null;
            }

            return view('plan.delete_confirm', [
                'plan_data' => $plan_data,
                'layout_set' => $layout_set,
                'share_user_name' => $share_user_name,
                ]);
        }
    }

    /**
     * 「削除内容確認」画面で「削除」をクリック時
     * 該当のレコードを削除し、「予定一覧」画面にリダイレクトする
     */
    public function deleteStore (Request $request)
    {
        $plan_exist = new PlanExist($request);
        $exist = $plan_exist->getResult();

        if(!$exist){
            
            \Session::flash('flash_msg', $this->not_exist);
            return redirect()->route('schedule');

        } else {

            $plan_delete = new PlanDelete($request);
            $result = $plan_delete->getResult();
            $date_redirect = $plan_delete->getRedirectDate();

            \Session::flash('flash_msg', $result);
            return redirect()->to($date_redirect);
        }
    }
}