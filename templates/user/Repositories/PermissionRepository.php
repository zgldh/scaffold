<?php namespace $NAME$\User\Repositories;

use InfyOm\Generator\Common\BaseRepository;
use Spatie\Permission\Models\Permission;
use zgldh\Scaffold\DataTablesData;

class PermissionRepository extends BaseRepository
{
    use DataTablesData;
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Permission::class;
    }
}
