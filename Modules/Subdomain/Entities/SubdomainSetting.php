<?php

namespace Modules\Subdomain\Entities;

use Illuminate\Database\Eloquent\Model;

class SubdomainSetting extends Model
{
    // region Properties

    protected $table = 'sub_domain_module_settings';

    protected $default = [
        'id',
    ];

}
