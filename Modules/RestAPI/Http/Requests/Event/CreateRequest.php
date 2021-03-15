<?php

namespace Modules\RestAPI\Http\Requests\Event;

use Modules\RestAPI\Http\Requests\BaseRequest;

class CreateRequest extends BaseRequest
{

	public function authorize()
	{
        $user = api_user();
        // Either user has role admin or has permission create_events
        // Plus he needs to have events module enabled from settings
        return in_array('events', $user->modules) && ($user->hasRole('admin') || $user->can('create_events'));
	}

	public function rules()
	{
		return [
            'event_name' => 'required',
            'where' => 'required',
            'description' => 'required',
            'start_date_time' => 'required',
            'end_date_time' => 'required',

		];
	}

}
