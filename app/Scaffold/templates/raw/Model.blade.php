<?php
        use App\Scaffold\Installer\Utils;
    /**
     * @var $MODEL \App\Scaffold\Installer\Model\ModelDefinition
     * @var $field  \App\Scaffold\Installer\Model\Field
     */
    $fillableFields = [];
    foreach($MODEL->getFields() as $field)
    {
        if ($field->isNotTableField()){
            continue;
        }
        $fillableFields[] = $field->getName();
    }

    $casts = [];
    foreach($MODEL->getFields() as $field)
    {
        if ($field->isNotTableField()){
            continue;
        }
        $casts[$field->getName()] = $field->getCastType();
    }

    $rules = [];
    foreach($MODEL->getFields() as $field)
    {
        $rules[$field->getName()] = $field->getRule();
    }
    echo '<?php' ?> namespace {{$NAME_SPACE}}\Models;

use Illuminate\Database\Eloquent\Model;
@if($MODEL->isActivityLog())
use Modules\ActivityLog\Traits\LogsActivity;
@endif
@if($MODEL->isSoftDelete())
use Illuminate\Database\Eloquent\SoftDeletes;
@endif

class {{$MODEL_NAME}} extends Model
{
@if($MODEL->isActivityLog())
    use LogsActivity;
@endif
@if($MODEL->isSoftDelete())
    use SoftDeletes;
@endif

    public $table = '{{$MODEL->getTable()}}';

    public $fillable = <?php echo Utils::exportArray($fillableFields);?>;
@if($MODEL->isActivityLog())

    protected static $logAttributes = <?php echo Utils::exportArray($fillableFields);?>;
@endif

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = <?php echo Utils::exportArray($casts);?>;
@if($MODEL->isSoftDelete())
    protected $dates = ['deleted_at'];
@endif

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = <?php echo Utils::exportArray($rules);?>;
<?php
    $existedRelationNames = [];
    foreach($MODEL->getFields() as $field):
        $relationship = $field->getRelationship();
        if(!$relationship):
            continue;
        endif;
        $relationshipType = $relationship['type'];
        unset($relationship['type']);
        $relationshipClassName = ucfirst(camel_case($relationshipType));
        $relatedName = $field->getRelationshipName($existedRelationNames);
        $existedRelationNames[] = $relatedName;
        $relationParams = Utils::arrayToString($relationship,',',"'");
        ?>
    /**
     * @return \Illuminate\Database\Eloquent\Relations\{{$relationshipClassName}}
     **/
    public function {{$relatedName}}()
    {
@if($where = $field->getWhere())
        return $this->{{$relationshipType}}({!! $relationParams !!})->where({!! Utils::arrayToString($where,',',"'"); !!});
@else
        return $this->{{$relationshipType}}({!! $relationParams !!});
@endif
    }
<?php endforeach;?>
}
