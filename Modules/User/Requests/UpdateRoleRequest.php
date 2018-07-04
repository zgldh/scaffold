<?php namespace Modules\User\Requests;

use Modules\User\Models\Role;
use Dingo\Api\Http\FormRequest;
use App\Scaffold\Traits\HasWithParameter;

class UpdateRoleRequest extends FormRequest
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
        $role = Role::find($this->route()->parameters['role']);
        $roleId = $role->id;

        $rules = Role::$rules;
        $rules['name'] = 'required|unique:z_roles,name,' . $roleId;
        return $rules;
    }

    public function attributes()
    {
        return __('role.fields');
    }
}
