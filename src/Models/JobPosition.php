<?php

declare(strict_types=1);

namespace Gomu\Auth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobPosition extends Model
{
    use SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'job_positions';

    public function getTable()
    {
        $schema = config('gomu-auth.schema');
        return $schema ? $schema . '.' . $this->table : $this->table;
    }

    protected $fillable = [
        'id',
        'title',
        'department_id',
        'job_level_id',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function jobLevel()
    {
        return $this->belongsTo(JobLevel::class);
    }
}