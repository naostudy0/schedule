<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShareRequest extends Model
{
    // テーブル名
    protected $table = 'share_requests';

    // 可変項目
    protected $fillable = 
    [
        'user_id',
        'requested_user_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
