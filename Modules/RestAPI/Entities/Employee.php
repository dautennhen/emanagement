<?php

namespace Modules\RestAPI\Entities;

use Illuminate\Support\Facades\Hash;

class Employee extends \App\User
{
    // region Properties


    protected $table = 'users';


    protected $default = [
        'id',
        'name',
        'email'
    ];

    protected $hidden = [
        'employee_detail.department_id',
        'employee_detail.designation_id'
    ];

    protected $guarded = [
        'id',
    ];

    protected $filterable = [
        'id',
        'users.name',
        'status'
    ];

    public function visibleTo(\App\User $user)
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return false;

    }

    public function scopeVisibility($query)
    {
        if (api_user()) {

            $user = api_user();

            $query->withoutGlobalScope('active')->join('role_user', 'role_user.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'role_user.role_id')
                ->select('users.id', 'users.name as name', 'users.email', 'users.created_at')
                ->where('roles.name', '<>', 'client')
                ->orderBy('users.id')
                ->groupBy('users.id');

            if ($user->hasRole('admin')) {
                return $query;
            }
        }
    }

    public function setPasswordAttribute($value)
    {
        if($value){
            $this->attributes['password'] = \Hash::make($value);
        }
    }

}
