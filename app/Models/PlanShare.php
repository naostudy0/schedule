<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanShare extends Model
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $primaryKey = 'plan_share_id';
    /**
     * @var string
     */
    protected $table = 'plan_share';

    /**
     * @var array
     */
    protected $fillable = [
        'plan_id',
        'plan_made_user_id',
        'plan_shared_user_id',
    ];
}
