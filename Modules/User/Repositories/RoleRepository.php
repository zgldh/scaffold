<?php namespace Modules\User\Repositories;

use App\Scaffold\BaseRepository;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Modules\User\Models\Role;

class RoleRepository extends BaseRepository
{
    const ROLE_SUPER_ADMIN = 'super-admin';

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'label'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Role::class;
    }

    /**
     * @return Role
     */
    public static function GET_SUPER_ADMIN()
    {
        return (app(self::class))->findWhere(['name' => self::ROLE_SUPER_ADMIN])->first();
    }

    /**
     * @no-permission
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes)
    {
        $permissions = [];
        if (isset($attributes['permissions'])) {
            $permissions = $attributes['permissions'];
            unset($attributes['permissions']);
        }
        $role = parent::create($attributes);

        if (count($permissions)) {
            $role = $this->update(['permissions' => $permissions, 'label' => $attributes['label']], $role->id);
        }

        return $role;
    }

    /**
     * @no-permission
     * @param array $attributes
     * @param $id
     * @return mixed
     */
    public function update(array $attributes, $id)
    {
        $role = parent::update($attributes, $id);
        if (isset($attributes['label'])) {
            $this->setRoleLanguage($id, \App::getLocale(), $attributes['label']);
            $role->setTempLabel($attributes['label']);
        }

        return $role;
    }

    /**
     * To update the role translated text
     * @param $role
     * @param $locale
     * @param $text
     * @throws FileNotFoundException
     */
    private function setRoleLanguage($role, $locale, $text)
    {
        if (is_numeric($role)) {
            $role = $this->find($role);
        }

        if (!$role) {
            return;
        }

        $roleName = $role->snake_case_name;
        $languageFilePath = resource_path('lang/' . $locale . '/role.php');
        if (!file_exists($languageFilePath)) {
            throw new FileNotFoundException("resources/lang/{$locale}/role.php doesn't exist.");
        }
        $languageContent = file_get_contents($languageFilePath);

        $text = addslashes($text);
        $regexp = "/('roles'.*=>[\w\W]*)('{$roleName}'\s*=>\s*')(.+)'/";
        $replaceExp = "$1$2{$text}'";

        $languageContent = preg_replace($regexp, $replaceExp, $languageContent);

        file_put_contents($languageFilePath, $languageContent);
    }

    /**
     * Copy the $oldRole to a new one with the $newRoleName
     * @no-permission
     * @param $oldRole
     * @param $newRoleName
     * @return Role
     */
    public function copy($oldRole, $newRoleName)
    {
        /**
         * @var Role $newRole
         */
        $newRole = new $this->model();
        $newRole->fill([
            'name'       => $newRoleName,
            'label'      => $newRoleName,
            'guard_name' => $oldRole->guard_name
        ]);
        $newRole->save();

        $newRole = $this->update(['permissions' => $oldRole->permissions->toArray()], $newRole->id);
        return $newRole;
    }
}
