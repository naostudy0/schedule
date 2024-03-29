<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ユーザーのステータスを表す数値
    | 0:仮登録 1:本登録 2:メール認証済 9:退会済
    |--------------------------------------------------------------------------
    */
    'user_status' => [
        'pre_register' => '0',
        'register'     => '1',
        'mail_authed'  => '2',
        'deactive'     => '9',
    ],

    /*
    |--------------------------------------------------------------------------
    | 予定作成で使用できる色と番号
    |--------------------------------------------------------------------------
    */
    'plan_color' => [
        'pink'      => '1',
        'orange'    => '2',
        'palegreen' => '3',
        'skyblue'   => '4',
        'tomato'    => '5',
        'gray'      => '6',
    ],
];
