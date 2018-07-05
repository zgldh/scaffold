<?php namespace Modules\User\Requests;

use App\Scaffold\Traits\HasWithParameter;
use Modules\User\Models\Role;
use Dingo\Api\Http\FormRequest;

class CreateRoleRequest extends FormRequest
{
    use HasWithParameter;

    /**
     * Determine if the role is authorized to make this request.
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
        $rules = Role::$rules;
        $rules['name'] = 'required|unique:z_roles';
        $rules['label'] = 'required|unique:z_roles';
        return $rules;
    }

    public function attributes()
    {
        return __('role.fields');
    }
}
