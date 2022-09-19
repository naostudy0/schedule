<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

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
        return $this->belongsTo('App\Models\User');
    }

    /**
     * 対象の予定が該当のユーザーのものか確認
     *
     * @param  int $plan_id
     * @param  int $user_id
     * @return bool
     */
    public function hasPlan($plan_id, $user_id)
    {
        return DB::table($this->table)
            ->where('id', $plan_id)
            ->where('user_id', $user_id)
            ->exists();
    }

    /**
     * 予定を取得して暗号化・複合化を行い表示可能な状態にして返す
     *
     * @param  int $plan_id
     * @return array
     */
    public function getOneRecord($plan_id)
    {
        $row = DB::table($this->table)
            ->select([
                'id',
                'content',
                'detail',
                'color',
                'start_date',
                'end_date',
                'start_time',
                'end_time',
                'share_user_id',
            ])
            ->where('id', $plan_id)
            ->first();

        return [
            // 暗号化
            'id' => Crypt::encrypt($row->id),
            // 複合化
            'content' => Crypt::decrypt($row->content),
            'detail'  => Crypt::decrypt($row->detail),

            'color'         => $row->color,
            'start_date'    => $row->start_date,
            'end_date'      => $row->end_date,
            'start_time'    => $row->start_time,
            'end_time'      => $row->end_time,
            'share_user_id' => $row->share_user_id,
        ];
    }
}
