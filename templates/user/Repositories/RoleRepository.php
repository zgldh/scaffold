<?php namespace $NAME$\User\Repositories;

use InfyOm\Generator\Common\BaseRepository;
use Spatie\Permission\Models\Role;
use zgldh\Scaffold\DataTablesData;

class RoleRepository extends BaseRepository
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
        return Role::class;
    }
}
