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
    protected $primaryKey = 'plan_id';
    /**
     * @var string
     */
    protected $table = 'plans';
    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'start_datetime',
        'end_datetime',
        'content',
        'detail',
        'color',
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
     * 予定を取得して複合化を行い表示可能な状態にして返す
     *
     * @param  int $plan_id [予定ID]
     * @return object
     */
    public function getOneRecord($plan_id)
    {
        $row = DB::table($this->table)
            ->select([
                "$this->table.plan_id",
                "$this->table.color",
                "$this->table.start_datetime",
                "$this->table.end_datetime",
                "$this->table.content as encrypt_content",
                "$this->table.detail as encrypt_detail",
                DB::raw("GROUP_CONCAT(plan_share.plan_shared_user_id) AS shared_user_ids"),
            ])
            ->leftjoin('plan_share', "$this->table.plan_id", '=', 'plan_share.plan_id')
            ->where("$this->table.plan_id", $plan_id)
            ->first();

        $row->content = Crypt::decrypt($row->encrypt_content);
        $row->detail  = Crypt::decrypt($row->encrypt_detail);

        return $row;
    }

    /**
     * 予定の保存処理
     *
     * @param \Illuminate\Http\Request $request
     * @param int $user_id [会員ID]
     */
    public function storePlan($request, $user_id)
    {
        $shared_user_ids = [];
        if ($request->input('share_user')) {
            foreach ($request->only('share_user') as $share_id){
                $shared_user_ids[] = DB::table('users')
                    ->where('share_id', $share_id)
                    ->value('user_id');
            }
        }

        $start_datetime = Carbon::parse($request->start_date . ' ' . $request->start_time)->format('Y-m-d H:i:s');
        $end_datetime   = Carbon::parse($request->end_date   . ' ' . $request->end_time)->format('Y-m-d H:i:s');

        $plan_id = DB::table($this->table)
            ->insertGetId([
                'user_id'        => $user_id,
                'start_datetime' => $start_datetime,
                'end_datetime'   => $end_datetime,
                'content'        => Crypt::encrypt($request->content),
                'detail'         => Crypt::encrypt($request->detail),
                'color'          => $request->color,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now(),
            ]);

        if ($shared_user_ids) {
            foreach ($shared_user_ids as $shared_user_id) {
                DB::table('plan_share')
                    ->insert([
                        'plan_id' => $plan_id,
                        'plan_made_user_id' => $user_id,
                        'plan_shared_user_id' => $shared_user_id,
                        'created_at'    => Carbon::now(),
                        'updated_at'    => Carbon::now(),
                    ]);
            }
        }
    }

    /**
     * 予定の更新処理
     *
     * @param \Illuminate\Http\Request $request
     */
    public function updatePlan($request, $user_id)
    {
        $shared_user_ids = [];
        if ($request->input('share_user')) {
            foreach ($request->only('share_user') as $share_id){
                $shared_user_ids[] = DB::table('users')
                    ->where('share_id', $share_id)
                    ->value('user_id');
            }
        }

        DB::table($this->table)
            ->where('plan_id', $request->plan_id)
            ->update([
                'start_datetime' => $request->start_date . ' ' . $request->start_time,
                'end_datetime' => $request->end_date . ' ' . $request->end_time,
                'color' => $request->color,
                'content' => Crypt::encrypt($request->content),
                'detail' => Crypt::encrypt($request->detail),
            ]);

        DB::table('plan_share')
            ->where('plan_id', $request->plan_id)
            ->delete();
        
        if ($shared_user_ids) {
            foreach ($shared_user_ids as $shared_user_id) {
                DB::table('plan_share')
                    ->insert([
                        'plan_id' => $request->plan_id,
                        'plan_made_user_id' => $user_id,
                        'plan_shared_user_id' => $shared_user_id,
                        'created_at'    => Carbon::now(),
                        'updated_at'    => Carbon::now(),
                    ]);
            }
        }
    }

    /**
     * 自分の予定と共有された予定を取得
     * 
     * @param  \Carbon\Carbon $carbon
     * @param  int            $user_id
     * @return Illuminate\Support\Collection
     */
    public function getMyPlansAndSharedPlans($carbon, $user_id)
    {
        $start_date = $carbon->copy()->startOfMonth();
        $end_date   = $carbon->copy()->endOfMonth();

        $rows = DB::table($this->table)
            ->select([
                "$this->table.plan_id",
                "$this->table.user_id",
                "$this->table.start_datetime",
                "$this->table.end_datetime",
                "$this->table.content as encrypt_content",
                "$this->table.detail as encrypt_detail",
                "$this->table.color",
                'plan_share.plan_made_user_id',
                'made_users.name as made_user_name',
                DB::raw("GROUP_CONCAT(plan_share.plan_shared_user_id) AS shared_user_ids"),
            ])
            ->leftjoin('plan_share', "$this->table.plan_id", '=', 'plan_share.plan_id')
            ->leftjoin('users as made_users', 'plan_share.plan_made_user_id', '=', 'made_users.user_id')
            ->whereBetween('start_datetime', [$start_date, $end_date])
            ->where(function ($query) use ($user_id) {
                $query->where("$this->table.user_id", $user_id)
                    ->orWhere('plan_share.plan_shared_user_id', $user_id);
            })
            ->orderBy("$this->table.start_datetime", 'asc')
            ->groupBy("$this->table.plan_id")
            ->get();

        foreach ($rows as &$row) {
            $row->content = Crypt::decrypt($row->encrypt_content);
            $row->detail  = Crypt::decrypt($row->encrypt_detail);
        }

        return $rows;
    }
}
