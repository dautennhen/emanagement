<?php

namespace Modules\RestAPI\Entities;

class Event extends \App\Event
{
    // region Properties

    protected $table = 'events';

    protected $default = [
        'id',
        'event_name',
        'where',
        'start_date_time',
        'end_date_time',
        'repeat',
    ];

    protected $guarded = [
        'id',
    ];

    protected $filterable = [
        'id',
        'event_name',
        'where',
        'start_date_time',
        'end_date_time',
        'repeat',
    ];


}
