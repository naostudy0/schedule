<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailUpdate extends Model
{
    /**
     * @var string
     */
    protected $table = 'email_updates';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'email',
        'email_update_token',
    ];

    /**
     * メールアドレス変更希望のユーザーを取得
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
