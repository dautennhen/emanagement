<?php

namespace Modules\RestAPI\Entities;

class Client extends User
{
    protected $table = 'users';



    public function scopeVisibility($query)
    {

        if (api_user()) {


            // If employee or client show projects assigned
            $query->join('role_user', 'role_user.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'role_user.role_id')
                ->leftJoin('client_details', 'users.id', '=', 'client_details.user_id')
                ->select('users.id', 'users.name as name', 'users.email', 'users.created_at', 'client_details.company_name', 'users.image')
                ->where('roles.name', 'client');

            return $query;
        }
        return $query;

    }

}
