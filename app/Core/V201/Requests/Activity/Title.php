<?php namespace App\Core\V201\Requests\Activity;

use Illuminate\Database\DatabaseManager;

/**
 * Class Title
 * @package App\Core\V201\Requests\Activity
 */
class Title extends ActivityBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules['narrative.*.narrative'] = 'required';
        $rules['narrative']             = 'unique_lang|unique_default_lang';

        return $rules;
    }

    /**
     * get the error message as required
     * @return array
     */
    public function messages()
    {
        $defaultFieldValues = app()->make(Databasemanager::class)->table('settings')->select('default_field_values')->where('organization_id', '=', session('org_id'))->first();
        $defaultLanguage    = $defaultFieldValues ? json_decode($defaultFieldValues->default_field_values, true)[0]['default_language'] : null;

        $messages['narrative.*.narrative.required'] = 'Title is required';
        $messages['narrative.unique_lang']          = 'Language should be unique.';

        return $messages;
    }
}
