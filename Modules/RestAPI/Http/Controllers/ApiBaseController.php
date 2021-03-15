<?php


namespace Modules\RestAPI\Http\Controllers;


use Froiden\RestAPI\ApiController;
use Froiden\RestAPI\ApiResponse;
use Modules\RestAPI\Entities\RestAPISetting;

class ApiBaseController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        // SET default guard to api
        // auth('api')->user will be accessed as auth()->user();
        config(['auth.defaults.guard' => 'api']);

        // Set JWT SECRET KEY HERE

        config(['jwt.secret' => config('restapi.jwt_secret')]);
        config(['app.debug' => config('restapi.debug')]);
    }
}
