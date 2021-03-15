<?php

ApiRoute::group(['namespace' => 'Modules\RestAPI\Http\Controllers'], function() {

    ApiRoute::get('app', ['as' => 'api.app', 'uses' => 'AppController@app']);

    // Forgot Password
    ApiRoute::post('auth/forgot-password', ['as' => 'api.auth.forgotPassword', 'uses' => 'AuthController@forgotPassword']);

    // Auth routes
    ApiRoute::post('auth/login', ['as' => 'api.auth.login', 'uses' => 'AuthController@login']);
    ApiRoute::post('auth/logout', ['as' => 'api.auth.logout', 'uses' => 'AuthController@logout']);
    ApiRoute::post('auth/reset-password', ['as' => 'api.auth.resetPassword', 'uses' => 'AuthController@resetPassword']);
    ApiRoute::get('auth/refresh', ['as' => 'api.auth.refresh', 'uses' => 'AuthController@refresh']);

});

ApiRoute::group(['namespace' => 'Modules\RestAPI\Http\Controllers','middleware' => 'api.auth'], function() {

    ApiRoute::get('company', ['as' => 'api.app', 'uses' => 'CompanyController@company']);
    ApiRoute::post('/project/{project_id}/members', ['as' => 'project.member', 'uses' => 'ProjectController@members']);
    ApiRoute::delete('/project/{project_id}/member/{id}', ['as' => 'project.member.delete', 'uses' => 'ProjectController@memberRemove']);
    ApiRoute::resource('project', 'ProjectController');
    ApiRoute::resource('project-category', 'ProjectCategoryController');
    ApiRoute::resource('currency', 'CurrencyController');

    ApiRoute::get('/task/remind/{id}', ['as' => 'task.remind', 'uses' => 'TaskController@remind']);

    ApiRoute::resource('/task/{task_id}/subtask', 'SubTaskController');
    ApiRoute::resource('task', 'TaskController');
    ApiRoute::resource('task-category', 'TaskCategoryController');
    ApiRoute::resource('taskboard-columns', 'TaskboardColumnController');


    ApiRoute::resource('lead', 'LeadController');
    ApiRoute::resource('client', 'ClientController');
    ApiRoute::resource('department', 'DepartmentController');
    ApiRoute::resource('designation', 'DesignationController');

    ApiRoute::resource('holiday', 'HolidayController');

    ApiRoute::resource('contract-type', 'ContractTypeController');
    ApiRoute::resource('contract', 'ContractController');

    ApiRoute::resource('notice', 'NoticeController');
    ApiRoute::resource('event', 'EventController');

    ApiRoute::resource('estimate', 'EstimateController');
    ApiRoute::resource('invoice', 'InvoiceController');

    ApiRoute::resource('product', 'ProductController');
    ApiRoute::resource('employee', 'EmployeeController');

    ApiRoute::post('/device/register', ['as' => 'device.register', 'uses' => 'DeviceController@register']);
    ApiRoute::post('/device/unregister', ['as' => 'device.unregister', 'uses' => 'DeviceController@unregister']);

    ApiRoute::get('/attendance/today', ['as' => 'attendance.today', 'uses' => 'AttendanceController@today']);
    ApiRoute::post('/attendance/clock-in', ['as' => 'attendance.clockIn', 'uses' => 'AttendanceController@clockIn']);

});
