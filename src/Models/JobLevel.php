<?php

declare(strict_types=1);

namespace Gomu\Auth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobLevel extends Model
{
    use SoftDeletes;

    protected $connection = 'auth';

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'job_levels';

    public function getTable()
    {
        return config('gomu-auth.schema', 'account') . '.' . $this->table;
    }

    protected $fillable = [
        'id',
        'level_name',
        'level_rank',
    ];
}