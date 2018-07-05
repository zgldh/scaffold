<?php
/**
 * @var $MODEL \zgldh\Scaffold\Installer\Model\ModelDefinition
 * @var $field  \zgldh\Scaffold\Installer\Model\FieldDefinition
 */
echo '<?php' ?> namespace {{$moduleNameSpace}}\Requests;

use Dingo\Api\Http\FormRequest;
use App\Scaffold\Traits\HasWithParameter;

class {{$className}} extends FormRequest
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
        return [
        //
        ];
    }

    public function attributes()
    {
        return [
        //
        ];
        // return __('some_u18n.fields');
    }
}
