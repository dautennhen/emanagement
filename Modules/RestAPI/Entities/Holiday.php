<?php

namespace Modules\RestAPI\Entities;


class Holiday extends \App\Holiday
{
    // region Properties

    protected $table = 'holidays';

    protected $default = [
        'id',
        'date',
        'occassion'
    ];

    protected $filterable = [
        'id',
        'date',
        'occassion'
    ];

    //endregion


}
