<?php

declare(strict_types=1);

namespace Gomu\Auth\Models;

use App\Models\EmployeeReport;
use App\Models\LeaveRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Employee extends Model
{
    use SoftDeletes, HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'employees';

    public function getTable()
    {
        $schema = config('gomu-auth.schema');
        return $schema ? $schema . '.' . $this->table : $this->table;
    }

    protected static function newFactory()
    {
        return \Database\Factories\EmployeeFactory::new();
    }

    protected $fillable = [
        'id',
        'user_id',
        'nip',
        'nik',
        'full_name',
        'gender',
        'date_of_birth',
        'phone_number',
        'personal_email',
        'address',
        'city',
        'state',
        'postal_code',
        'district',
        'village',
        'department_id',
        'join_date',
        'termination_date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function assignments()
    {
        return $this->hasMany(EmployeeAssignment::class);
    }

    public function reports()
    {
        return $this->hasMany(EmployeeReport::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    // Get primary job position (current assignment)
    public function primaryAssignment()
    {
        return $this->hasOne(EmployeeAssignment::class)
            ->where('is_primary', true)
            ->orderBy('start_date', 'desc');
    }

    // Get current job position
    public function currentJobPosition()
    {
        return $this->primaryAssignment()
            ->with('jobPosition')
            ->first()
            ?->jobPosition;
    }

    // Get colleague employees in same department
    public function getColleaguesInDepartment()
    {
        $currentPosition = $this->currentJobPosition();
        if (!$currentPosition) {
            return collect([]);
        }

        return Employee::whereHas('assignments', function ($query) use ($currentPosition) {
            $query->where('is_primary', true)
                ->whereHas('jobPosition', function ($q) use ($currentPosition) {
                    $q->where('department_id', $currentPosition->department_id);
                });
        })
            ->where('id', '!=', $this->id)
            ->with('user.role')
            ->orderBy('full_name')
            ->get();
    }

    // Get colleagues based on user's role hierarchy
    public function getColleaguesByRole()
    {
        $currentPosition = $this->currentJobPosition();
        if (!$currentPosition || !$this->user || !$this->user->role) {
            return collect([]);
        }

        $roleName = $this->user->role->role_name;
        $departmentId = $currentPosition->department_id;

        // Staff: only same department
        if ($roleName === 'Staff') {
            return $this->getColleaguesInDepartment();
        }

        // Manager/Head/Supervisor: include sub-departments
        if (in_array($roleName, ['Manager', 'Head', 'Supervisor', 'Admin'])) {
            $department = Department::find($departmentId);
            if (!$department) {
                return $this->getColleaguesInDepartment();
            }

            $departmentIds = $department->getAllDescendantIds();
            $departmentIds[] = $departmentId;

            return Employee::whereHas('assignments', function ($query) use ($departmentIds) {
                $query->where('is_primary', true)
                    ->whereHas('jobPosition', function ($q) use ($departmentIds) {
                        $q->whereIn('department_id', $departmentIds);
                    });
            })
                ->where('id', '!=', $this->id)
                ->with(['user.role', 'primaryAssignment.jobPosition.department'])
                ->orderBy('full_name')
                ->get();
        }

        // Default: same department only
        return $this->getColleaguesInDepartment();
    }
}