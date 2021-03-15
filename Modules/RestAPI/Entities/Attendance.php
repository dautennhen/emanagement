<?php

namespace Modules\RestAPI\Entities;

class Attendance extends \App\Attendance
{
    // region Properties

    protected $table = 'attendances';

    protected $default = [
        'id',
    ];

    protected $hidden = [
        'company_id',
    ];

    protected $guarded = [
        'id',
        'company_id',
    ];

    protected $filterable = [
        'id',
    ];


}
