<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailUpdate extends Model
{
    // テーブル名
    protected $table = 'email_updates';

    // 可変項目
    protected $fillable = 
    [
        'user_id',
        'email',
        'email_update_token',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
