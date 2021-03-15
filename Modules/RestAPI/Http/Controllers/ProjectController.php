<?php

namespace Modules\RestAPI\Http\Controllers;

use Froiden\RestAPI\ApiResponse;
use Modules\RestAPI\Entities\Project;
use Modules\RestAPI\Http\Requests\Projects\IndexRequest;
use Modules\RestAPI\Http\Requests\Projects\CreateRequest;
use Modules\RestAPI\Http\Requests\Projects\UpdateRequest;
use Modules\RestAPI\Http\Requests\Projects\ShowRequest;
use Modules\RestAPI\Http\Requests\Projects\DeleteRequest;

class ProjectController extends ApiBaseController
{
    protected $model = Project::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = CreateRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $showRequest = ShowRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    public function modifyIndex($query)
    {
        return $query->visibility();
    }

    public function members($projectId)
    {
        $project =  Project::find($projectId);
        if (request()->get('members')) {
            $ids = array_column(request()->get('members'),'id');
            $project->members_many()->sync($ids);
        }

        return ApiResponse::make('Project member added successfully');
    }
    public function memberRemove($projectId,$id)
    {
        $project =  Project::find($projectId);
        $project->members_many()->detach($id);

        return ApiResponse::make('Member removed');
    }
}
