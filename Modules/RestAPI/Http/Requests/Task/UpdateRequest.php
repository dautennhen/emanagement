<?php

namespace Modules\RestAPI\Http\Requests\Task;

use Modules\RestAPI\Http\Requests\BaseRequest;

class UpdateRequest extends BaseRequest
{

	public function authorize()
	{
        $user = api_user();

        // Either user has role admin or has permission view_projects
        // Plus he needs to have projects module enabled from settings

        return in_array('tasks', $user->modules) && ($user->hasRole('admin') || $user->can('edit_tasks'));
	}

	public function rules()
	{
		return [
            'task_users.*.id' => 'required|exists:users,id',
            'project.id' => 'sometimes|required|exists:projects,id',
            'heading' => 'sometimes|required',
            'start_date' => 'sometimes|sometimes|nullable|date',
            'priority' =>'sometimes|required|in:medium,high,low',
            'due_date' => 'sometimes|required|date',
            'category.id' => 'sometimes|sometimes|exists:task_category,id',
            'is_private' =>'boolean',
		];
	}

}
