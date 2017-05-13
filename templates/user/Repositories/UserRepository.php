<?php namespace $NAME$\User\Repositories;

use $NAME$\User\Models\User;
use InfyOm\Generator\Common\BaseRepository;
use zgldh\Scaffold\DataTablesData;

class UserRepository extends BaseRepository
{
    use DataTablesData;
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'email',
        'password',
        'remember_token'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return User::class;
    }
}
