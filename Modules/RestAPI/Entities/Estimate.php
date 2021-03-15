<?php

namespace Modules\RestAPI\Entities;

class Estimate extends \App\Estimate
{
    // region Properties

    protected $table = 'estimates';

    protected $default = [
        'id',
        'estimate_number',
        'total',
        'status',
        'valid_till',
    ];

    protected $guarded = [
        'id',
    ];

    protected $filterable = [
        'id',
        'status'
    ];

    public function visibleTo(\App\User $user)
    {
        if ($user->hasRole('admin') || ($user->user_other_role !== 'employee' && $user->can('view_estimates'))) {
            return true;
        }
        return $this->client_id == $user->id;

    }

    public function scopeVisibility($query)
    {
        if(api_user()) {
            $user = api_user();

            if($user->hasRole('client')){
                $query->where('estimates.client_id', $user->id);
            }

            return $query;
        }
    }
}
