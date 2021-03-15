<?php

namespace Modules\RestAPI\Entities;

class Invoice extends \App\Estimate
{
    // region Properties

    protected $table = 'invoices';

    protected $default = [
        'id',
        'invoice_number',
        'total',
        'status',
        'issue_date',
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

            if ($user->hasRole('admin')) {
                return $query;
            }

            else{
                // If employee or client show projects assigned
                $query->leftJoin('projects', 'invoices.project_id', '=', 'projects.id')
                    ->join('project_members', 'project_members.project_id', '=', 'projects.id')
                    ->where(function($query)use($user) {
                        $query->where('project_members.user_id', $user->id)
                              ->orWhere('invoices.client_id', $user->id);
                    });

                return $query;
            }
        }
        return $query;
    }
}
