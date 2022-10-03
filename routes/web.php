<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// 非ログインTOP
Route::view('/', 'index')->name('index');

Auth::routes();

// 確認メールからアクセス
Route::get('register/verify/{token}', 'Auth\RegisterController@showForm');

// 会員登録
Route::group([
    'prefix' => 'register',
    'as' => 'register.'
], function () {
    // 仮登録
    Route::post('pre_check', 'Auth\RegisterController@pre_check')->name('pre_check');
    // 本登録
    Route::post('main_check', 'Auth\RegisterController@mainCheck')->name('main.check');
    Route::post('main_register', 'Auth\RegisterController@mainRegister')->name('main.registered');
});

// 予定管理画面
Route::get('/schedule', 'ScheduleController@show')->name('schedule')->middleware('auth');

// 予定管理
Route::group([
    'prefix' => 'plan',
    'as' => 'plan.',
    'middleware' => 'auth'
], function () {
    // 作成
    Route::get('create', 'PlanController@planCreate')->name('create');
    Route::post('create_confirm', 'PlanController@planConfirm')->name('confirm');
    Route::post('create', 'PlanController@planReCreate')->name('recreate');
    Route::post('store', 'PlanController@planStore')->name('store');
    // 更新
    Route::get('update', 'PlanController@planUpdate')->name('update');
    Route::post('update_confirm', 'PlanController@updateConfirm')->name('update.confirm');
    Route::post('update_store', 'PlanController@updateStore')->name('update.store');
    Route::post('update', 'PlanController@planReUpdate')->name('reupdate');
    // 削除
    Route::get('delete_confirm', 'PlanController@deleteConfirm')->name('delete.confirm');
    Route::get('delete', 'PlanController@deleteStore')->name('delete');
});

// 会員情報
Route::group([
    'prefix' => 'customer',
    'as' => 'customer.',
    'middleware' => 'auth'
], function () {
    // 会員情報一覧
    Route::view('/', 'customer.index')->name('index');
    // 名前変更
    Route::view('registed_name_update', 'customer.name_update')->name('name.update');
    Route::post('registed_name_store', 'CustomerController@registedNameStore')->name('name.store');
    // パスワード変更
    Route::view('registed_password_update', 'customer.password_update')->name('password.update');
    Route::post('registed_password_store', 'CustomerController@registedPasswordStore')->name('password.store');
    // メールアドレス変更
    Route::view('registed_email_update', 'customer.email_update_check')->name('email.update');
    Route::post('registed_email_confirm', 'CustomerController@registedEmailConfirm')->name('email.confirm');
    // 退会
    Route::view('delete_check', 'customer.delete.account_delete_confirm')->name('delete.confirm');
    Route::get('delete_store', 'CustomerController@accountDeleteStore')->name('delete.store');
});

Route::get('/customer/registed_email/verify/{token}', 'CustomerController@emailUpdated')->name('customer.email.store');

// 予定共有
Route::group([
    'prefix' => 'share',
    'as' => 'share.',
    'middleware' => 'auth'
], function () {
    Route::get('/', 'ShareController@show')->name('index');
    Route::post('/change-id', 'ShareController@changeId')->name('change_id');
    Route::post('/change-permit', 'ShareController@changePermit')->name('change_permit');
    Route::post('/request', 'ShareController@shareSearch')->name('request');
    Route::post('/request/permit', 'ShareController@sharePermit')->name('request.permit');
    Route::post('/send', 'ShareController@shareSend')->name('send');
    Route::post('/send/delete', 'ShareController@shareSendDelete')->name('send.delete');
    Route::post('/delete', 'ShareController@shareDelete')->name('delete');
});

Route::get('/schedule-new/{any?}', function () {
    return view('schedule-new.index');
})->where('any', '.*');
