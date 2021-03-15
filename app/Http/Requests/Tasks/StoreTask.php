<?php

namespace App\Http\Requests\Tasks;

use App\Company;
use App\Http\Requests\CoreRequest;
use App\Project;
use App\Task;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class StoreTask extends CoreRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $setting = Company::with('currency', 'package')->withoutGlobalScope('active')->where('id', Auth::user()->company_id)->first();

        Validator::extend('check_date_format', function($attribute, $value, $parameters, $validator)  use ($setting) {
            try {
                Carbon::createFromFormat($setting->date_format, $value)->format('Y-m-d');
            } catch (\Throwable $th) {
                return false;
            }

            return true;
        });

        Validator::extend('check_after_or_equal', function($attribute, $value, $parameters, $validator)  use ($setting) {
           $currentValueDate =  Carbon::createFromFormat($setting->date_format, $value);
           $compareToDate =  Carbon::createFromFormat($setting->date_format,  $parameters[0]);

            if($currentValueDate->greaterThanOrEqualTo($compareToDate)){
                return true;
            }
            return false;
        });

        Validator::extend('check_after', function($attribute, $value, $parameters, $validator)  use ($setting) {
           $currentValueDate =  Carbon::createFromFormat($setting->date_format, $value);
           $compareToDate =  Carbon::createFromFormat($setting->date_format,  $parameters[0]);

            if($currentValueDate->greaterThan($compareToDate)){
                return true;
            }
            return false;
        });

        $user = auth()->user();
        $rules = [
            'heading' => 'required',
            'due_date' => 'required|check_date_format|check_after_or_equal:'.$this->get('start_date'),
            'priority' => 'required',
            'user_id.0' => 'required'
        ];

        if ($user->cans('add_tasks') || $user->hasRole('admin')) {
            $rules['user_id'] = 'required';
        }

        if (request()->has('project_id') && request()->project_id != "all" && request()->project_id != "") {
            $project = Project::find(request()->project_id);
            $startDate = $project->start_date->format($setting->date_format);
            $rules['start_date'] = 'required|check_date_format|check_after_or_equal:'.$startDate;
        } else {
            $rules['start_date'] = 'required|check_date_format';
        }

        if ($this->has('dependent') && $this->dependent == 'yes' && $this->dependent_task_id != '') {
            $dependentTask = Task::find($this->dependent_task_id);

            $rules['start_date'] = 'required|check_after:'.$dependentTask->due_date->format($setting->date_format);
        }

        if ($this->has('repeat') && $this->repeat == 'yes') {
            $rules['repeat_cycles'] = 'required|numeric';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'project_id.required' => __('messages.chooseProject'),
            'user_id.required' => 'Choose an assignee',
            'user_id.0.required' => 'Choose an assignee',
            'due_date.check_after_or_equal' => 'The due date must be a date after or equal to start date.',
            'start_date.check_after_or_equal' => 'The start date must be a date after or equal to project start date.',
            'start_date.check_after' => 'The start date must be a date after to parent task due date.'
        ];
    }
}
