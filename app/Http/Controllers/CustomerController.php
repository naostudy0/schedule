<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Support\Facades\Mail;
use App\User;
use App\Models\EmailUpdate;
use App\Models\Plan;
use Auth;
use App\Mail\EmailUpdateConfirm;


class CustomerController extends Controller
{
    public function show()
    {
        return view('customer.show');
    }

    public function registedNameUpdate()
    {
        return view('customer.name_update');
    }

    public function registedNameStore(Request $request)
    {
        $name = $request->validate(['name' => 'required|string']);

        $id = Auth::id();
        
        $user = User::where('id', $id)->first();
        $user->name = $name['name'];

        $user->save();

        \Session::flash('flash_msg', '登録情報を更新しました。');
        return redirect()->route('customer.show');
    }

    public function registedPasswordUpdate()
    {
        return view('customer.password_update');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function passwordValidator(array $data)
    {
        return Validator::make($data, [
            'password_now' => ['required', 'string', 'min:8'],
            'password_new' => ['required', 'string', 'min:8'],
            'password_new_confirm' => ['required', 'string', 'min:8', 'same:password_new'],
        ]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function emailValidator(array $data)
    {
        return Validator::make($data, [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        ]);
    }

    public function registedPasswordStore(Request $request)
    {
        $this->passwordValidator($request->all())->validate();
        $id = Auth::id();

        $user = User::where('id', $id)->first();

        if(!Hash::check($request->password_now, $user->password))
        {
            $validator = Validator::make([], []);
            $validator->errors()->add('password_now', 'パスワードが一致しません');
            return redirect()->back()->withErrors($validator->errors())->withInput();

        } else {
            $password = Hash::make($request->password_new);

            $user->password = $password;
            $user->save();

            \Session::flash('flash_msg', '登録情報を更新しました。');
            return redirect()->route('customer.show');
        }
    }

    public function registedEmailUpdate()
    {
        return view('customer.email_update_check');
    }

    public function registedEmailConfirm(Request $request)
    {
        $this->emailValidator($request->all())->validate();

        $token = base64_encode($request->email);
        $email_update = EmailUpdate::create([
            'user_id' => Auth::id(),
            'email' => $request->email,
            'email_update_token' => $token,
        ]);

        $email = new EmailUpdateConfirm($email_update);
        Mail::to($email_update->email)->send($email);

        return view('customer.email_update_confirm');
    }

    public function emailUpdated($token)
    {
        if(!EmailUpdate::where('email_update_token',$token)->exists())
        {
            // トークンが存在しない場合
            $result = '無効なトークンです';
            return view('customer.email_update_result', ['result' => $result]);
    
        } else {
            $email_new = EmailUpdate::where('email_update_token', $token)->first();
            
            if(User::where('email', $email_new->email)->exists())
            {
                // 新しいメールアドレスが既に本登録されている場合
                $result = 'このメールアドレスは既に登録済みです';
                return view('customer.email_update_result', ['result' => $result]);

            } else {
                // 上記に該当しなかった場合にメールアドレスを変更する
                $user = User::where('id', $email_new->user_id)->first();
    
                $user->email = $email_new->email;
                $user->save();

                $result = 'メールアドレスの変更が完了しました';
                return view('customer.email_update_result', ['result' => $result]);
            }
        }
    }

    public function accountDeleteConfirm()
    {
        return view('customer.delete.account_delete_confirm');
    }

    public function accountDeleteStore()
    {
        $user_id = Auth::id();
        Auth::logout();

        // メールアドレスの変更依頼削除
        $email_update = EmailUpdate::where('user_id', $user_id)->delete();
        // 予定削除
        $plan = Plan::where('user_id', $user_id)->delete();
        
        // statusを退会済み「9」に変更
        $user = User::where('id', $user_id)->first();
        $user->status = '9';
        $user->save();

        return view('customer.delete.account_delete');
    }
}