<?php
/**
 * @var $MODEL \App\Scaffold\Installer\Model\ModelDefinition
 * @var $field  \App\Scaffold\Installer\Model\FieldDefinition
 */
echo '<?php' ?> namespace {{$NAME_SPACE}}\Requests;

use {{$NAME_SPACE}}\Models\{{$MODEL_NAME}};
use Illuminate\Foundation\Http\FormRequest;
use App\Scaffold\Traits\HasWithParameter;

class Create{{$MODEL_NAME}}Request extends FormRequest
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
        $rules = {{$MODEL_NAME}}::$rules;
<?php
    foreach($MODEL->getFields() as $field)
    {
        if(!$field->isUnique()){
            continue;
        }
        $validations = [];
        if($field->getValidations()){
            $validations[] = $field->getValidations();
        }
        if($field->isRequired()){
            $validations[] = 'required';
        }
        if($field->isUnique()){
            $table = $MODEL->getTable();
            $validations[] = "unique:$table";
        }
        $validations = join('|',$validations);
?>
        $rules['{{$field->getName()}}'] = '{{$validations}}';
<?php } ?>
        return $rules;
    }

    public function attributes()
    {
        return __('<?php echo $MODEL->getModelLang().'.fields'; ?>');
    }
}
