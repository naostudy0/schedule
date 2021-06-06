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

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// 仮会員登録
Route::post('register/pre_check', 'Auth\RegisterController@pre_check')->name('register.pre_check');

// 確認メールからアクセス
Route::get('register/verify/{token}', 'Auth\RegisterController@showForm');

// 本会員登録
Route::post('register/main_check', 'Auth\RegisterController@mainCheck')->name('register.main.check');
Route::post('register/main_register', 'Auth\RegisterController@mainRegister')->name('register.main.registered');

Route::get('/schedule', 'ScheduleController@show')->name('schedule')->middleware('auth');

// 予定作成
Route::get('/plan/create', 'PlanController@planCreate')->name('plan.create')->middleware('auth');
Route::post('/plan/create_confirm', 'PlanController@planConfirm')->name('plan.confirm')->middleware('auth');
Route::post('/plan/create', 'PlanController@planReCreate')->name('plan.recreate')->middleware('auth');
Route::post('/plan/store', 'PlanController@planStore')->name('plan.store')->middleware('auth');

// 予定更新
Route::get('/plan/update', 'PlanController@planUpdate')->name('plan.update');
Route::post('/plan/update_confirm', 'PlanController@updateConfirm')->name('plan.update.confirm')->middleware('auth');
Route::post('/plan/update_store', 'PlanController@updateStore')->name('plan.update.store')->middleware('auth');
Route::post('/plan/update', 'PlanController@planReUpdate')->name('plan.reupdate')->middleware('auth');

// 予定削除
Route::get('/plan/delete_confirm', 'PlanController@deleteConfirm')->name('plan.delete.confirm')->middleware('auth');
Route::get('/plan/delete', 'PlanController@deleteStore')->name('plan.delete')->middleware('auth');

// 会員情報
Route::get('/customer', 'CustomerController@show')->name('customer.show')->middleware('auth');

// 名前変更
Route::get('/customer/registed_name_update', 'CustomerController@registedNameUpdate')->name('customer.name.update')->middleware('auth');
Route::post('/customer/registed_name_store', 'CustomerController@registedNameStore')->name('customer.name.store')->middleware('auth');

// パスワード変更
Route::get('/customer/registed_password_update', 'CustomerController@registedPasswordUpdate')->name('customer.password.update')->middleware('auth');
Route::post('/customer/registed_password_store', 'CustomerController@registedPasswordStore')->name('customer.password.store')->middleware('auth');

// メールアドレス変更
Route::get('/customer/registed_email_update', 'CustomerController@registedEmailUpdate')->name('customer.email.update')->middleware('auth');
Route::post('/customer/registed_email_confirm', 'CustomerController@registedEmailConfirm')->name('customer.email.confirm')->middleware('auth');
Route::get('/customer/registed_email/verify/{token}', 'CustomerController@emailUpdated')->name('customer.email.store');

// 退会
Route::get('/customer/delete_check', 'CustomerController@accountDeleteConfirm')->name('customer.delete.confirm')->middleware('auth');
Route::get('/customer/delete_store', 'CustomerController@accountDeleteStore')->name('customer.delete.store')->middleware('auth');