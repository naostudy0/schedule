<?php

// セッションを使用したAPI用のルート
Route::get('/schedule', 'Api\ApiScheduleController@index');
Route::post('/schedule/store', 'Api\ApiScheduleController@store');
