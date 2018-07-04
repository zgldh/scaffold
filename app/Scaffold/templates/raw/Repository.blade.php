<?php
use App\Scaffold\Installer\Utils;
    /**
     * @var $MODEL \App\Scaffold\Installer\Model\ModelDefinition
     * @var $field  \App\Scaffold\Installer\Model\FieldDefinition
     */
    $searchableFields = [];
    foreach($MODEL->getFields() as $field)
    {
        if(!$field->isNotSearchable())
        {
            $searchableFields[] = $field->getName();
        }
    }
    echo '<?php' ?> namespace {{$NAME_SPACE}}\Repositories;

use {{$NAME_SPACE}}\Models\{{$MODEL_NAME}};
use App\Scaffold\BaseRepository;

class {{$MODEL_NAME}}Repository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = <?php echo Utils::exportArray($searchableFields);?>;

    /**
     * Configure the Model
     **/
    public function model()
    {
        return {{$MODEL_NAME}}::class;
    }
}
