<?php
/**
 * @var $MODEL \zgldh\Scaffold\Installer\Model\ModelDefinition
 * @var $field  \zgldh\Scaffold\Installer\Model\FieldDefinition
 */
echo '<?php' ?> namespace {{$moduleNameSpace}}\Controllers;

use Illuminate\Http\JsonResponse;
use App\Scaffold\AppBaseController;

class {{$className}} extends AppBaseController
{
    public function __construct()
    {
        $this->middleware("auth:api");
    }
}
