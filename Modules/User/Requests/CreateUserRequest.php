<?php namespace Modules\User\Requests;

use App\Scaffold\Traits\HasWithParameter;
use Modules\User\Models\User;
use Dingo\Api\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
        $rules = User::$rules;
        $rules['name'] = 'required';//|unique:z_users
        $rules['email'] = [
            "required",
            "unique:z_users,email,NULL,id,deleted_at,NULL"
        ];
        return $rules;
    }

    public function attributes()
    {
        return __('user.fields');
    }
}
