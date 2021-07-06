<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    // テーブル名
    protected $table = 'plans';

    // 可変項目
    protected $fillable = 
    [
        'user_id',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'content',
        'detail',
        'share_user_id',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
