<?php

namespace Modules\RestAPI\Entities;


use App\Observers\ProjectObserver;
use App\User;
use Illuminate\Database\Eloquent\Builder;

class Project extends \App\Project
{


    protected $default = [
        'id',
        'project_name',
        'start_date',
        'deadline',
        'status'
    ];

    protected $dates = [
        'start_date',
        'deadline',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'category_id',
        'client_id',
    ];

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
        'category_id',
        'client_id',
        'hours_logged'
    ];

    protected $filterable = [
        'id',
        'project_name',
        'start_date',
        'deadline',
        'status'
    ];


    protected $appends = ['isProjectAdmin','hours_logged'];
    // Override the functions
    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'project_id');
    }

    public function visibleTo(\App\User $user)
    {
        if ($user->hasRole('admin') || ($user->user_other_role !== 'employee' && $user->can('view_projects'))) {
            return true;
        }
        return in_array($user->id, $this->members->pluck('user_id')->all());

    }

    public function scopeVisibility($query)
    {
        if(api_user()) {

            $user = api_user();

            if ($user->hasRole('admin') || ($user->user_other_role !== 'employee' && $user->can('view_projects'))) {
                return $query;
            }

            else if($user->hasRole('client')){
                $query->where('projects.client_id', $user->id);
            }
            else{
                // If employee or client show projects assigned
                $query->whereIn('projects.id', function($query) use($user) {
                    $query->select(\DB::raw('DISTINCT(`projects`.`id`)'))
                        ->from('projects')
                        ->join('project_members', 'project_members.project_id', '=', 'projects.id')
                        ->where('project_members.user_id', $user->id);
                });

                return $query;
            }
            return $query;
        }
    }

    public function getHoursLoggedAttribute()
    {
        $totalMinutes = $this->times()->sum('total_minutes');
        return intdiv($totalMinutes, 60);
    }

}
