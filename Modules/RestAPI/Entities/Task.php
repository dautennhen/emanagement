<?php

namespace Modules\RestAPI\Entities;


use App\TaskboardColumn;

class Task extends \App\Task
{
    // region Properties

    protected $table = 'tasks';
    protected $fillable= ['heading','start_date','priority','due_date','is_private','status','board_column_id'];
    protected $appends = ['all_board_columns'];

    protected $default = [
        'id',
        'heading',
        'start_date',
        'priority',
        'due_date',
        'is_private',
        'status',
    ];

    protected $hidden = [
        'user_id',
        'project_id',
        'task_category_id',
        'created_by'
    ];

    protected $guarded = [
        'id',
    ];

    protected $filterable = [
        'id',
        'heading',
        'project_id',
        'board_column_id'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'task_users');
    }

    public function getAllBoardColumnsAttribute()
    {
        return TaskboardColumn::all();
    }

    public function visibleTo(\App\User $user)
    {

        if ($user->hasRole('admin') ) {
            return true;
        }
        if (in_array($user->id, [$this->created_by]) || $this->is_private === 0) {
            return true;
        }

        $task = Task::join('projects', 'tasks.project_id', '=', 'projects.id')
            ->join('project_members', 'project_members.project_id', '=', 'projects.id')
            ->join('task_users', 'task_users.user_id', '=', 'tasks.id')
            ->where('project_members.user_id', $user->id)
            ->orWhere('task_users.user_id', $this->id)
            ->get();

        if(!$task->isEmpty()){
            return true;
        }
        return false;

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
                $query->join('projects', 'tasks.project_id', '=', 'projects.id')
                      ->join('project_members', 'project_members.project_id', '=', 'projects.id')
                      ->join('task_users', 'task_users.user_id', '=', 'tasks.id')
                      ->where('project_members.user_id', $user->id);
                $query->orWhere('task_users.user_id', $user->id);
                $query->orWhere('created_by', $user->id);
                $query->orWhere('is_private', 0);


                return $query;
            }
        }
        return $query;
    }


}
