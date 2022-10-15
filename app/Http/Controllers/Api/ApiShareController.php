<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\Http\Controllers\Controller;
use App\Models\ShareUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiShareController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_id = Auth::id();
        // 予定共有しているユーザーを取得
        $share_users_data = $this->share_user->getShareUserData($user_id);
        // 自分に届いている未回答の予定共有依頼を取得
        $requested_user_data = $this->share_user->getShareRequestNotReplied($user_id, true);
        // 自分が申請した未回答の予定共有依頼を取得
        $requesting_user_data = $this->share_user->getShareRequestNotReplied($user_id, false);

        return [
            'share_users_data' => $share_users_data,
            'requesting_user_data' => $requesting_user_data,
            'requested_user_data' => $requested_user_data,
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
