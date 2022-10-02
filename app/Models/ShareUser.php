<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShareUser extends Model
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $primaryKey = 'share_user_id';
    /**
     * @var string
     */
    protected $table = 'share_users';
    /**
     * @var array
     */
    protected $fillable = [
        'requested_user_id',
        'received_user_id',
        'is_replied',
        'is_shared',
    ];

    /**
     * 予定共有しているユーザー情報を取得
     * 
     * @param  int $user_id [会員ID]
     * @return null|array
     */
    public function getShareUserData($user_id)
    {
        $rows = DB::table($this->table)
            ->select([
                'requested_user_id',
                'received_user_id',
                'is_shared',
            ])
            ->where(function ($query) use ($user_id) {
                $query->where('requested_user_id', $user_id);
                $query->orWhere('received_user_id', $user_id);
            })
            ->where('is_shared', 1)
            ->whereNull('deleted_at')
            ->get();

        if ($rows->isEmpty()) {
            return;
        }

        foreach ($rows as $row) {
            $target_id = $row->requested_user_id === $user_id ? $row->received_user_id : $row->requested_user_id;
            $share_user_data[] = DB::table('users')
                ->select([
                    'name',
                    'share_id',
                ])
                ->where('user_id', $target_id)
                ->first();
        }
        return $share_user_data;
    }

    /**
     * 未回答の共有申請を取得
     * 
     * @param  int  $user_id [会員ID]
     * @param  bool $to_me   [自分宛か]
     * @return null|array
     */
    public function getShareRequestNotReplied($user_id, $to_me)
    {
        $select_column = $to_me ? 'requested_user_id' : 'received_user_id';
        $where_column  = $to_me ? 'received_user_id' : 'requested_user_id';

        $target_ids = DB::table($this->table)
            ->where($where_column, $user_id)
            ->where('is_replied', 0)
            ->whereNull('deleted_at')
            ->pluck($select_column);

        if ($target_ids->isEmpty()) {
            return;
        }

        foreach ($target_ids as $target_id) {
            $share_user_data[] = DB::table('users')
                ->select([
                    'name',
                    'share_id',
                ])
                ->where('user_id', $target_id)
                ->first();
        }
        return $share_user_data;
    }

    /**
     * 予定共有を許可・拒否する
     * 
     * @param  int    $user_id  [会員ID]
     * @param  string $share_id [共有用ID]
     * @param  int    $permit   [拒否:0 | 許可:1]
     * @return bool
     */
    public function updateShareUser($user_id, $share_id, $permit)
    {
        $target_id = DB::table('users')
            ->where('share_id', $share_id)
            ->where('share_permission', 1)
            ->value('id');

        $row = DB::table($this->table)
            ->where('requested_user_id', $target_id)
            ->where('received_user_id', $user_id)
            ->where('is_replied', 0)
            ->whereNull('deleted_at')
            ->value('share_user_id');

        if (! $row) {
            return false;
        }

        DB::table($this->table)
            ->where('share_user_id', $row)
            ->update([
                'is_replied' => 1,
                'is_shared' => $permit,
            ]);

        return true;
    }

    /**
     * 予定共有を解除する
     * 
     * @param int    $user_id  [会員ID]
     * @param string $share_id [共有用ID]
     */
    public function releaseShareUser($user_id, $share_id)
    {
        $share_user_id = DB::table('users')
            ->where('share_id', $share_id)
            ->value('id');

        DB::table($this->table)
            ->where(function ($query) use ($user_id, $share_user_id) {
                $query->where('requested_user_id', $user_id);
                $query->orWhere('received_user_id', $share_user_id);
            })
            ->orWhere(function ($query) use ($user_id, $share_user_id) {
                $query->where('received_user_id', $user_id);
                $query->orWhere('requested_user_id', $share_user_id);
            })
            ->where('is_shared', 1)
            ->whereNull('deleted_at')
            ->update([
                'deleted_at' => Carbon::now()
            ]);
    }
}
