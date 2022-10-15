<?php

// セッションを使用したAPI用のルート
Route::get('/schedule', 'Api\ApiScheduleController@index');
Route::get('/setting', 'Api\ApiSettingController@index');
Route::get('/share', 'Api\ApiShareController@index');
