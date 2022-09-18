<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use App\Mail\EmailVerification;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * 入力されたメールアドレスをバリデーション
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users']
        ]);
    }

    /**
     * 入力されたメールアドレスをバリデーションして、確認画面を表示
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\View\View
     */
    public function pre_check(Request $request)
    {
        if (! User::where('email', $request->email)->exists() || User::where('email', $request->email)->where('status', '1')->exists()) {
            $this->validator($request->all())->validate();
        }

        $request->flashOnly('email');

        $bridge_request = $request->all();
        return view('auth.register_check')->with($bridge_request);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return App\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'email' => $data['email'],
            'email_verify_token' => base64_encode($data['email']),
        ]);

        return $user;
    }

    /**
     * 入力されたメールアドレスが新規ユーザか確認
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\View\View
     */
    public function register(Request $request)
    {
        // 既に本登録済みのメールアドレスの場合（以前の画面が残っていた場合などに発生）
        if ((User::where('email', $request->email)->where('status', '1')->exists())) {
            return view('auth.login');
        }

        // 退会済みユーザの場合
        if (User::where('email', $request->email)->where('status', '9')->exists()) {
            $user = User::where('email', $request->email)->first();
            $user->status = '0';
            $user->save();

        // 新規ユーザの場合
        } else {
            event(new Registered($user = $this->create($request->all())));
        }

        $email = new EmailVerification($user);
        Mail::to($user->email)->send($email);

        return view('auth.registered');
    }

    /**
     * クエリパラメータのtokenとDBのemail_verify_tokenを照らし合わせて一致すれば本会員登録画面へ
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\View\View
     */
    public function showForm($email_token)
    {
        // 登録されたemail_verify_tokenと一致するものがない場合
        if (! User::where('email_verify_token',$email_token)->exists()) {
            return view('auth.main.register')->with('message', '無効なトークンです。');
        }

        $user = User::where('email_verify_token', $email_token)->first();

        // 既に本登録されている場合
        if ($user->status == config('const.USER_STATUS.REGISTER')) {
            logger("status". $user->status );
            return view('auth.main.register')->with('message', 'すでに本登録されています。ログインして利用してください。');
        }

        // statusを更新して本登録画面へ
        $user->status = config('const.USER_STATUS.MAIL_AUTHED');
        try {
            $user->save();
            return view('auth.main.register', compact('email_token'));
        } catch (Exception $e) {
            return view('auth.main.register')->with('message', 'メール認証に失敗しました。再度、メールに記載されたリンクをクリックしてください。');
        }
    }

    /**
     * 「名前」「パスワード」をバリデーションして確認画面へ遷移
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\View\View
     */
    public function mainCheck(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $email_token = $request->email_token;

        $user = new User();
        $user->name = $request->name;
        $user->password = $request->password;

        return view('auth.main.register_check', compact('user','email_token'));
    }

    /**
     * email_verify_tokenが一致するレコードにユーザ情報を登録
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\View\View
     */
    public function mainRegister(Request $request)
    {
        $user = User::where('email_verify_token',$request->email_token)->first();
        $user->status = config('const.USER_STATUS.REGISTER');
        $user->name = $request->name;
        $user->password = Hash::make($request->password);
        $user->email_verified_at = date('Y-m-d H:i:s');
        $user->save();

        return view('auth.main.registered');
    }
}
