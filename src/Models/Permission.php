<?php

declare(strict_types=1);

namespace Gomu\Auth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use SoftDeletes;

    protected $connection = 'auth';

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'permissions';

    public function getTable()
    {
        return config('gomu-auth.schema', 'account') . '.' . $this->table;
    }

    protected $fillable = [
        'id',
        'permission_key',
        'module_name',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'permission_id', 'role_id');
    }
}