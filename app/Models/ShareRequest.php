<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShareRequest extends Model
{
    /**
     * @var string
     */
    protected $table = 'share_requests';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'requested_user_id',
        'status',
    ];

    /**
     * 予定共有希望のユーザーを取得
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
