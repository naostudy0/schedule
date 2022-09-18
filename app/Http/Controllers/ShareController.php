<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Share\ShareRequested;
use App\Share\ShareUserList;
use App\Share\ShareSearch;
use App\Share\ChangeStatus;
use App\Share\SharePermit;
use App\Share\ShareShow;
use App\Share\ShareSend;
use App\Share\ShareDelete;
use App\Share\ShareSendDelete;
use Auth;

class ShareController extends Controller
{
    /**
     * 予定共有管理画面の表示
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        // share_id（ユーザ検索用ID）と、ユーザ検索画面の表示可否を取得
        $share_show = new ShareShow;
        $share = $share_show->getData();

        $id = Auth::id();

        // 予定共有しているユーザのshare_id（ユーザ検索用ID）と名前を取得
        $share_user_list = new ShareUserList($id);
        $share_users_data = $share_user_list->getData();

        // 自分に届いている未回答の予定共有依頼を取得
        $share_requested = new ShareRequested('requested_user_id');
        $requested_user_data = $share_requested->getData();
        if ($requested_user_data) {
            \Session::flash('request', '予定共有の申請が届いています。');
        }

        // 自分が申請して未回答の予定共有依頼を取得
        $share_request = new ShareRequested('user_id');
        $requesting_user_data = $share_request->getData();

        return view('share.show',[
            'share' => $share,
            'share_users_data' => $share_users_data,
            'requesting_user_data' => $requesting_user_data,
            'requested_user_data' => $requested_user_data,
        ]);
    }

    /**
     * ユーザ検索表示可否、またはshare_id（ユーザ検索用ID）変更
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeShare(Request $request)
    {
        // ステータスを変更し、画面表示するメッセージを取得（失敗時は失敗のメッセージ）
        $change_status = new ChangeStatus($request);
        $msg = $change_status->getMessage();

        \Session::flash('flash_msg', $msg);
        return redirect()->to('/share');
    }

    /**
     * ユーザ検索
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function shareSearch(Request $request)
    {
        // ユーザ検索して結果を取得（誤ったIDが送信されたり、相手が表示拒否している場合はfalse）
        $share_search = new ShareSearch($request);
        $result = $share_search->getResult();

        if (! $result) {
            $msg = $share_search->getMessage();

            \Session::flash('flash_msg', $msg);
            return redirect()->to('/share');
        }

        $user_result = $share_search->getData();

        return view('share.request',[
            'user_result' => $user_result,
        ]);
    }

    /**
     * 自分へ届いた共有申請を承認または拒否する
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sharePermit(Request $request)
    {
        // ステータスを変更し、画面表示するメッセージを取得（失敗時は失敗のメッセージ）
        $share_permit = new SharePermit($request);
        $msg = $share_permit->getMessage();

        \Session::flash('flash_msg', $msg);
        return redirect()->to('/share');
    }

    /**
     * 共有申請する
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function shareSend(Request $request)
    {
        $share_id = $request->input('share_id');

        // ステータスを変更し、画面表示するメッセージを取得（失敗時は失敗のメッセージ）
        $share_send = new ShareSend($share_id);
        $msg = $share_send->getMessage();

        \Session::flash('flash_msg', $msg);
        return redirect()->to('/share');
    }

    /**
     * 自分が出した共有申請を取り消す
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function shareSendDelete(Request $request)
    {
        $own_id = Auth::id();
        $share_id = $request->input('share_id');

        $share_send_delete = new ShareSendDelete($own_id, $share_id);
        $msg = $share_send_delete->getMessage();

        \Session::flash('flash_msg', $msg);
        return redirect()->to('/share');
    }

    /**
     * 予定共有を解除
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function shareDelete(Request $request)
    {
        $share_id = $request->input('share_id');

        $share_delete = new ShareDelete($share_id);
        $msg = $share_delete->getMessage();

        \Session::flash('flash_msg', $msg);
        return redirect()->to('/share');
    }
}
