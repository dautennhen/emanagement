<?php

namespace Modules\RestAPI\Http\Requests\Event;

use Modules\RestAPI\Http\Requests\BaseRequest;

class DeleteRequest extends BaseRequest
{

	public function authorize()
	{
        $user = api_user();
        // Either user has role admin or has permission delete_events
        // Plus he needs to have events module enabled from settings
        return in_array('events', $user->modules) && ($user->hasRole('admin') || $user->can('delete_events'));
	}

	public function rules()
	{
		return [
			//
		];
	}

}
