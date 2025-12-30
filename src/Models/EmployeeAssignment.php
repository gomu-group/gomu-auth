<?php

declare(strict_types=1);

namespace Gomu\Auth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeAssignment extends Model
{
    use SoftDeletes;

    protected $connection = 'auth';

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'employee_assignments';

    public function getTable()
    {
        return config('gomu-auth.schema', 'account') . '.' . $this->table;
    }

    protected $fillable = [
        'id',
        'employee_id',
        'position_id',
        'status',
        'start_date',
        'end_date',
        'is_primary',
        'notes',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function position()
    {
        return $this->belongsTo(JobPosition::class, 'position_id');
    }

    // Alias for position relationship
    public function jobPosition()
    {
        return $this->position();
    }
}