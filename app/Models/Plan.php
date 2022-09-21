<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
     * 予定が存在する場合は予定IDを取得
     *
     * @param  int $plan_id_encrypted [暗号化された予定ID]
     * @param  int $user_id           [会員ID]
     * @return int|bool
     */
    public function getIdIfExists($plan_id_encrypted, $user_id)
    {
        $plan_id = Crypt::decrypt($plan_id_encrypted);

        $exists = DB::table($this->table)
            ->where('id', $plan_id)
            ->where('user_id', $user_id)
            ->exists();

        return $exists ? $plan_id : false;
    }

    /**
     * 予定を取得して暗号化・複合化を行い表示可能な状態にして返す
     *
     * @param  int $plan_id [予定ID]
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

    /**
     * 予定の保存処理
     *
     * @param \Illuminate\Http\Request $request
     * @param int $user_id [会員ID]
     */
    public function storePlan($request, $user_id)
    {
        DB::table($this->table)
            ->insert([
                'user_id'       => $user_id,
                'start_date'    => $request->start_date,
                'end_date'      => $request->end_date,
                'start_time'    => $request->start_time,
                'end_time'      => $request->end_time,
                'content'       => Crypt::encrypt($request->content),
                'detail'        => Crypt::encrypt($request->detail),
                'color'         => $request->color,
                'share_user_id' => $request->share_user_id,
                'created_at'    => Carbon::now(),
                'updated_at'    => Carbon::now(),
            ]);
    }

    /**
     * 予定の更新処理
     *
     * @param \Illuminate\Http\Request $request
     */
    public function updatePlan($request)
    {
        $share_user_id = null;
        if ($request->input('share_user')) {
            foreach ($request->only('share_user') as $share_id){
                $share_ids[] = DB::table('user')
                    ->where('share_id', $share_id)
                    ->value('user_id');
            }
            $share_user_id = ',' . implode(',', $share_ids) . ',';
        }

        $plan_id = Crypt::decrypt($request->input('id'));
        DB::table($this->table)
            ->where('id', $plan_id)
            ->update([
                'start_date' => $request->start_date,
                'start_time' => $request->start_time,
                'end_date' => $request->end_date,
                'end_time' => $request->end_time,
                'color' => $request->color,
                'content' => Crypt::encrypt($request->content),
                'detail' => Crypt::encrypt($request->detail),
                'share_user_id' => $share_user_id,
            ]);
    }
}
