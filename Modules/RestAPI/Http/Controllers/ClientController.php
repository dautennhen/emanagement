<?php

namespace Modules\RestAPI\Http\Controllers;

use App\Role;
use Froiden\RestAPI\ApiController;
use Illuminate\Support\Facades\Hash;
use Modules\RestAPI\Entities\Client;
use Modules\RestAPI\Entities\Employee;
use Modules\RestAPI\Entities\User;
use Modules\RestAPI\Http\Requests\Client\IndexRequest;
use Modules\RestAPI\Http\Requests\Client\CreateRequest;
use Modules\RestAPI\Http\Requests\Client\UpdateRequest;
use Modules\RestAPI\Http\Requests\Client\ShowRequest;
use Modules\RestAPI\Http\Requests\Client\DeleteRequest;

class ClientController extends ApiBaseController
{
    protected $model = Client::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = CreateRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $showRequest = ShowRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    public function modifyIndex($query)
    {
        return $query->visibility();
    }

    public function stored(Client $user)
    {

        $user->client_details()->create(request()->all());
        $clientRole = Role::where('name','client')->first();
        $user->attachRole($clientRole);
        return $user;
    }
}
