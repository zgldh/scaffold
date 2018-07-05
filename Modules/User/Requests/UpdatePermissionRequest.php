<?php namespace Modules\User\Requests;

use Modules\User\Models\Permission;
use Dingo\Api\Http\FormRequest;
use App\Scaffold\Traits\HasWithParameter;

class UpdatePermissionRequest extends FormRequest
{
    use HasWithParameter;

    /**
     * Determine if the permission is authorized to make this request.
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
        $permission = Permission::find($this->route()->parameters['permission']);
        $permissionId = $permission->id;

        $rules = Permission::$rules;
        $rules['name'] = 'required|unique:z_permissions,name,' . $permissionId;
        return $rules;
    }

    public function attributes()
    {
        return __('permission.fields');
    }
}
