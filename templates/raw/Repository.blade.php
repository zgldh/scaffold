<?php
    /**
     * @var $MODEL \zgldh\Scaffold\Installer\Model\ModelDefinition
     * @var $field  \zgldh\Scaffold\Installer\Model\FieldDefinition
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

use {{$NAME_SPACE}}\Modules\{{$MODEL_NAME}};
use zgldh\Scaffold\BaseRepository;

class {{$MODEL_NAME}}Repository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = <?php echo json_encode($searchableFields, JSON_PRETTY_PRINT);?>;

    /**
     * Configure the Model
     **/
    public function model()
    {
        return {{$MODEL_NAME}}::class;
    }
}
