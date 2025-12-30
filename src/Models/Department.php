<?php

declare(strict_types=1);

namespace Gomu\Auth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use SoftDeletes;

    protected $connection = 'auth';

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'departments';

    public function getTable()
    {
        return config('gomu-auth.schema', 'account') . '.' . $this->table;
    }

    protected $fillable = [
        'id',
        'name',
        'code',
    ];

    // Get all employees in this department
    public function employees()
    {
        return $this->hasMany(Employee::class, 'department_id');
    }

    // Get all descendant department IDs recursively
    public function getAllDescendantIds()
    {
        $ids = [];
        foreach ($this->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $child->getAllDescendantIds());
        }
        return $ids;
    }
}