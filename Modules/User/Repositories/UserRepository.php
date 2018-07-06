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

    public function create(array $attributes)
    {
        $user = parent::create($attributes);
        if (count($attributes['roles']) > 0) {
            $newRoles = Role::whereIn('id', $attributes['roles'])->get();
            $user->syncRoles($newRoles);
        }
        return $user;
    }

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
