<?php namespace Modules\User\Requests;

use Dingo\Api\Http\FormRequest;
use App\Scaffold\Traits\HasWithParameter;
use Modules\User\Models\Role;

class PostCopyRoleRequest extends FormRequest
{
    use HasWithParameter;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return  bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return  array
     */
    public function rules()
    {
        $table = (new Role)->table;
        return [
            //
            'id'   => 'required|exists:' . $table,
            'name' => 'required|unique:' . $table
        ];
    }

    public function attributes()
    {
        return __('role.fields');
    }
}
