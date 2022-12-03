<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified',
        'email_verify_token',
        'share_id',
        'share_permission',
        'share_user_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * 予定テーブルとリレーション
     */
    public function plans()
    {
        return $this->hasMany(Plan::class);
    }

    /**
     * メールアドレス更新テーブルとリレーション
     */
    public function emailUpdates()
    {
        return $this->hasMany(EmailUpdate::class);
    }

    /**
     * 予定共有申請テーブルとリレーション
     */
    public function shareRequest()
    {
        return $this->hasMany(ShareRequest::class);
    }
}
