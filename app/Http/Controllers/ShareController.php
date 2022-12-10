<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\ShareUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ShareController extends Controller
{
    /**
     * @var \App\Models\User
     */
    private $user;
    /**
     * @var \App\Models\ShareUser
     */
    private $share_user;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->user = new User;
        $this->share_user = new ShareUser;
    }

    /**
     * 予定共有管理画面の表示
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        $user_id = Auth::id();
        // 予定共有しているユーザーを取得
        $share_users_data = $this->share_user->getShareUserData($user_id);
        // 自分に届いている未回答の予定共有依頼を取得
        $requested_user_data = $this->share_user->getShareRequestNotReplied($user_id, true);
        if ($requested_user_data) {
            session()->flash('request', '予定共有の申請が届いています。');
        }
        // 自分が申請した未回答の予定共有依頼を取得
        $requesting_user_data = $this->share_user->getShareRequestNotReplied($user_id, false);

        return view('share.show',[
            'share_users_data' => $share_users_data,
            'requesting_user_data' => $requesting_user_data,
            'requested_user_data' => $requested_user_data,
        ]);
    }

    /**
     * 共有ユーザーIDの変更処理
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeId(Request $request)
    {
        $share_id = '';
        $result = false;

        if ($request->input('share_id') && ! $request->input('random')) {
            $validator = Validator::make($request->all(),[
                'share_id' => ['required', 'max:8', 'unique:users,share_id', 'regex:/^[a-zA-Z0-9]+$/'],
            ]);
            if (! $validator->fails()) {
                $share_id = $request->input('share_id');
            }
        } elseif (! $request->input('share_id') && $request->input('random')) {
            do {
                $rand = md5(uniqid());
                $share_id = substr($rand, 0, 8);
            } while ($this->user->where('share_id', $share_id)->exists());
        }

        if ($share_id) {
            $this->user->where('user_id', Auth::id())
                ->update([
                    'share_id' => $share_id
                ]);
            $result = true;
        }
        $msg = $result ? 'ユーザーIDを変更しました' : '8文字以内の英数字ではないか、他のユーザーに使用されているIDです';

        return redirect()->route('share.index')->with('flash_msg', $msg);
    }

    /**
     * ユーザ検索表示可否の変更処理
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePermit(Request $request)
    {
        $permit = (int)$request->input('share_permission');
        if (! ($permit === 0 || $permit === 1)) {
            return redirect()->back();
        }

        $user = Auth::user();
        $user->share_permission = $permit;
        $user->save();

        return redirect()->route('share.index')->with('flash_msg', 'ステータスを更新しました');
    }

    /**
     * ユーザ検索
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function shareSearch(Request $request)
    {
        $share_id = $request->input('share_id');
        // share_idが1~8文字の英数字かどうか
        $validator = Validator::make([$share_id],[
            'share_id' => ['between:1,8', 'regex:/^[a-zA-Z0-9]+$/'],
        ]);

        $result = false;
        if ($validator->fails()) {
            $msg = '8文字以内の英数字を入力してください。';
        } else {
            if (! $this->user->where('share_id', $share_id)->exists()) {
                $msg = '該当のユーザーが見つかりません。';
            } else {
                if ($share_id === Auth::user()->share_id){
                    $msg = 'あなたのIDです。';
                // 該当のユーザが予定共有を許可していない場合
                } elseif (! $this->user->where('share_id', $share_id)->where('share_permission', 1)->exists()) {
                    $msg = '該当のユーザーが見つかりません。';
                } else {
                    $result = true;
                }
            }
        }

        if (! $result) {
            return redirect()->route('share.index')->with('flash_msg', $msg);
        }

        return view('share.request',[
            'user_result' => $this->user->where('share_id', $share_id)->where('share_permission', 1)->first()
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
        $permit = $request->input('permit');
        if ($permit !== '0' && $permit !== '1') {
            return redirect()->back();
        }

        $share_id = $request->input('share_id');
        $result = $this->share_user->updateShareUser(Auth::id(), $share_id, $permit);

        $msg = $result ? '予定共有を承認しました' : '予定共有を拒否しました';
        return redirect()->route('share.index')->with('flash_msg', $msg);
    }

    /**
     * 共有申請
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function shareSend(Request $request)
    {
        $target_id = $this->user->where('share_id', $request->input('share_id'))->where('share_permission', 1)->value('user_id');
        if (! $target_id) {
            return redirect()->route('share.index')->with('flash_msg', '対象のユーザーが見つかりません');
        }

        $user_id = Auth::id();
        if ($target_id === $user_id) {
            return redirect()->route('share.index')->with('flash_msg', 'あなたのIDです');
        }

        $is_requested = $this->share_user->where('requested_user_id', $user_id)
                            ->where('received_user_id', $target_id)
                            ->where('is_replied', 0)
                            ->exists();
        if ($is_requested) {
            return redirect()->route('share.index')->with('flash_msg', '既に申請済みです');
        }

        $share_user_id = $this->share_user->where('received_user_id', $user_id)
                            ->where('requested_user_id', $target_id)
                            ->where('is_replied', 0)
                            ->value('share_user_id');
        if ($share_user_id) {
            $this->share_user->where('share_user_id', $share_user_id)
                ->update([
                    'is_replied' => 1,
                    'is_shared'  => 1,
                    'updated_at' => Carbon::now(),
                ]);
            return redirect()->route('share.index')->with('flash_msg', '予定共有を許可しました');
        }

        $result = false;
        DB::beginTransaction();
        try {
            $this->share_user->create([
                'requested_user_id' => $user_id,
                'received_user_id'  => $target_id,
                'is_replied' => 0,
                'is_shared'  => 0,
            ]);
            if (Auth::user()->share_permission === 0) {
                // 共有を許可していない場合は許可
                $this->user->where('user_id', Auth::id())
                    ->update([
                        'share_permission' => 1
                    ]);
            }
            DB::commit();
            $result = true;
        } catch (\Exception $e) {
            DB::rollback();
        }
        $flash_msg = $result ? '予定共有申請が完了しました' : '予定共有申請に失敗しました';

        return redirect()->route('share.index')->with('flash_msg', $flash_msg);
    }

    /**
     * 自分が出した共有申請を取り消す
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function shareSendDelete(Request $request)
    {
        $share_id = $request->input('share_id');

        $received_user_id = $this->user->where('share_id', $share_id)->value('user_id');
        $this->share_user->where('requested_user_id', Auth::id())
            ->where('received_user_id', $received_user_id)
            ->delete();

        return redirect()->route('share.index')->with('flash_msg', '申請を取り消しました。');
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
        $this->share_user->releaseShareUser(Auth::id(), $share_id);

        return redirect()->route('share.index')->with('flash_msg', '予定共有を解除しました');
    }
}
