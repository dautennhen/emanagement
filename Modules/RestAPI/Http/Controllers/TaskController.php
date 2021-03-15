<?php

namespace Modules\RestAPI\Http\Controllers;

use App\Events\TaskReminderEvent;
use App\Helper\Reply;
use App\Notifications\TaskReminder;
use App\Role;
use App\TaskUser;
use App\User;
use Froiden\RestAPI\ApiController;
use Froiden\RestAPI\ApiResponse;
use Illuminate\Support\Facades\Notification;
use Modules\RestAPI\Entities\Employee;
use Modules\RestAPI\Entities\Task;
use Modules\RestAPI\Http\Requests\Task\IndexRequest;
use Modules\RestAPI\Http\Requests\Task\CreateRequest;
use Modules\RestAPI\Http\Requests\Task\ShowRequest;
use Modules\RestAPI\Http\Requests\Task\UpdateRequest;
use Modules\RestAPI\Http\Requests\Task\DeleteRequest;

class TaskController extends ApiBaseController
{

    protected $model = Task::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = CreateRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $showRequest = ShowRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    public function modifyIndex($query)
    {
        return $query->visibility();
    }

    public function stored(Task $task)
    {
        return $this->syncTaskUsers($task);
    }

    public function updated(Task $task)
    {
        return $this->syncTaskUsers($task);
    }

    private function syncTaskUsers(Task $task){
        // To add custom fields data
        if (request()->get('task_users')) {
            $ids = array_column(request()->get('task_users'),'id');
            $task->users()->sync($ids);
        }

        return $task;
    }

    public function remind($taskID)
    {
        $task = \App\Task::findOrFail($taskID);
        event(new TaskReminderEvent($task));
        return ApiResponse::make(__('messages.reminderMailSuccess'));
    }

}
