<?php namespace Modules\User\Requests;

use Modules\User\Models\User;
use Dingo\Api\Http\FormRequest;
use App\Scaffold\Traits\HasWithParameter;

class UpdateUserRequest extends FormRequest
{
    use HasWithParameter;

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
        $user = User::find($this->route()->parameters['user']);
        $user_id = $user->id;

        $rules = User::$rules;
        $rules['name'] = 'required|unique:z_users,name,' . $user_id;
        $rules['email'] = 'email|unique:z_users,email,' . $user_id;
        unset($rules['password']);
        return $rules;
    }

    public function attributes()
    {
        return __('user.fields');
    }
}
