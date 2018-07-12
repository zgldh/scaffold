<?php namespace Modules\User\Repositories;

use App\Scaffold\BaseRepository;
use Modules\User\Models\Role;
use Modules\User\Models\User;

class UserRepository extends BaseRepository
{
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

    /**
     * @no-permission
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes)
    {
        $user = parent::create($attributes);
        if (count($attributes['roles']) > 0) {
            $newRoles = Role::whereIn('id', $attributes['roles'])->get();
            $user->syncRoles($newRoles);
        }
        return $user;
    }

    /**
     * @no-permission
     * @param array $attributes
     * @param $id
     * @return mixed
     */
    public function update(array $attributes, $id)
    {
        if (!(isset($attributes['password']) && strlen($attributes['password']) > 0)) {
            unset($attributes['password']);
        }

        $user = parent::update($attributes, $id);

        if (count($attributes['roles']) > 0) {
            $newRoles = Role::whereIn('id', $attributes['roles'])->get();
            $user->syncRoles($newRoles);
        }
        return $user;
    }
}
