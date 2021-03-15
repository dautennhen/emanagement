<?php namespace Modules\RestAPI\Http\Requests\Client;

use Modules\RestAPI\Http\Requests\BaseRequest;
class UpdateRequest extends BaseRequest
{

    /**
     * @return bool
     * @throws \Froiden\RestAPI\Exceptions\UnauthorizedException
     */
    public function authorize()
    {
        $user = api_user();
        return in_array('clients', $user->modules) && ($user->hasRole('admin') || $user->can('edit_clients'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'sometimes|required',
            'email' => 'sometimes|required|email',
            'company_name' => 'sometimes|required|email',
        ];
    }

    public function messages()
    {
        return [
            //
        ];
    }

}
