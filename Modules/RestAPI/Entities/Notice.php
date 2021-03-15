<?php

namespace Modules\RestAPI\Entities;


class Notice extends \App\Notice
{
    // region Properties

    protected $table = 'notices';

    protected $default = [
        'id',
        'heading',
    ];

    protected $filterable = [
        'id',
        'heading'
    ];

    public function visibleTo(\App\User $user)
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        else if ($user->hasRole('client')) {
            return $this->to === 'client';
        }

        if ($user->hasRole('employee')) {
            return $this->to === 'employee';
        }

        return true;

    }

    public function scopeVisibility($query)
    {
        if(api_user()) {

            $user = api_user();

            if($user->hasRole('admin')){
                return $query;
            }
            elseif($user->hasRole('client')){
                $query->where('notices.to', 'client');
            }
            elseif($user->hasRole('employee')){
                $query->where('notices.to', 'employee');
            }


        }
    }
}
