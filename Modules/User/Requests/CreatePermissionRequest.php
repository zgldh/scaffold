<?php namespace Modules\User\Requests;

use App\Scaffold\Traits\HasWithParameter;
use Modules\User\Models\Permission;
use Dingo\Api\Http\FormRequest;

class CreatePermissionRequest extends FormRequest
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
        $rules = Permission::$rules;
        $rules['name'] = 'required|unique:z_permissions';
        $rules['label'] = 'required|unique:z_permissions';
        return $rules;
    }

    public function attributes()
    {
        return __('permission.fields');
    }
}
