<?php

namespace Modules\RestAPI\Http\Controllers;

use Froiden\RestAPI\ApiController;
use Modules\RestAPI\Entities\Event;
use Modules\RestAPI\Http\Requests\Event\IndexRequest;
use Modules\RestAPI\Http\Requests\Event\CreateRequest;
use Modules\RestAPI\Http\Requests\Event\ShowRequest;
use Modules\RestAPI\Http\Requests\Event\UpdateRequest;
use Modules\RestAPI\Http\Requests\Event\DeleteRequest;

class EventController extends ApiBaseController
{

    protected $model = Event::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = CreateRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $showRequest = ShowRequest::class;
    protected $deleteRequest = DeleteRequest::class;

}
