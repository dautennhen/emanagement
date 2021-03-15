<?php namespace Modules\RestAPI\Http\Requests\Projects;

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
        // Either user has role admin or has permission view_projects
        // Plus he needs to have projects module enabled from settings
        return in_array('projects', $user->modules) && ($user->hasRole('admin') || $user->can('edit_projects'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'project_name' => 'sometimes|required',
            'start_date' => 'sometimes|required',
            'hours_allocated' => 'nullable|numeric',
        ];

        if (!$this->has('without_deadline')) {
            $rules['deadline'] = 'sometimes|required';
        }
        if ($this->project_budget != '') {
            $rules['project_budget'] = 'numeric';
            $rules['currency_id'] = 'sometimes|required';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            //
        ];
    }

}
