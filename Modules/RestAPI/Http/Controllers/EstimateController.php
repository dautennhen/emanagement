<?php

namespace Modules\RestAPI\Http\Controllers;

use Modules\RestAPI\Entities\Estimate;
use Modules\RestAPI\Http\Requests\Estimate\IndexRequest;
use Modules\RestAPI\Http\Requests\Estimate\CreateRequest;
use Modules\RestAPI\Http\Requests\Estimate\ShowRequest;
use Modules\RestAPI\Http\Requests\Estimate\UpdateRequest;
use Modules\RestAPI\Http\Requests\Estimate\DeleteRequest;

class EstimateController extends ApiBaseController
{

    protected $model = Estimate::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = CreateRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $showRequest = ShowRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    public function modifyIndex($query)
    {
        return $query->visibility();
    }

}
