<?php namespace App\Lite\Services\Validation\Rules\V202;

/**
 * Class Password
 * @package App\Lite\Services\Validation\Rules\V202
 */
class Password
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'oldPassword'      => 'required|min:6',
            'newPassword'      => 'required|min:6',
            'newPasswordAgain' => 'required|min:6|same:newPassword'
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'oldPassword.required'      => trans('validation.required', ['attribute' => trans('lite/profile.old_password')]),
            'oldPassword.min'           => trans('validation.min.string', ['attribute' => trans('lite/profile.old_password'), 'min' => '6']),
            'newPassword.required'      => trans('validation.required', ['attribute' => trans('lite/profile.new_password')]),
            'newPassword.min'           => trans('validation.min.string', ['attribute' => trans('lite/profile.new_password'), 'min' => '6']),
            'newPasswordAgain.same'     => trans('validation.same', ['attribute' => trans('lite/profile.old_password')]),
            'newPasswordAgain.required' => trans('validation.required', ['attribute' => trans('lite/profile.new_password_again')]),
            'newPasswordAgain.min'      => trans('validation.min.string', ['attribute' => trans('lite/profile.new_password_again'), 'min' => '6'])
        ];
    }
}
