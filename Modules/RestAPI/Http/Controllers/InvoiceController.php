<?php

namespace Modules\RestAPI\Http\Controllers;

use Modules\RestAPI\Entities\Invoice;
use Modules\RestAPI\Http\Requests\Invoice\IndexRequest;
use Modules\RestAPI\Http\Requests\Invoice\CreateRequest;
use Modules\RestAPI\Http\Requests\Invoice\ShowRequest;
use Modules\RestAPI\Http\Requests\Invoice\UpdateRequest;
use Modules\RestAPI\Http\Requests\Invoice\DeleteRequest;

class InvoiceController extends ApiBaseController
{

    protected $model = Invoice::class;

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
