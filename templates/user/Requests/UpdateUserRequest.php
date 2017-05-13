<?php namespace $NAME$\User\Requests;

use Illuminate\Foundation\Http\FormRequest;
use $NAME$\User\Models\User;

class UpdateUserRequest extends FormRequest
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
        $rules = User::$rules;
        unset($rules['password']);
        return $rules;
    }
}
