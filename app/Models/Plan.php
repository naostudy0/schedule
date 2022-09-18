<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    /**
     * @var string
     */
    protected $table = 'plans';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'content',
        'detail',
        'share_user_id',
    ];

    /**
     * 予定を登録したユーザーを取得
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
