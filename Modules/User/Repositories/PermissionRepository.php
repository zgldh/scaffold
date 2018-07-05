<?php namespace Modules\User\Repositories;

use App\Scaffold\BaseRepository;
use App\Scaffold\Installer\Utils;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Modules\User\Models\Permission;
use Modules\User\Models\Role;
use ReflectionMethod;

class PermissionRepository extends BaseRepository
{
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
        return Permission::class;
    }

    public static function GENERATE_PERMISSION_CODE($model, $methodName)
    {
        $model = ucfirst(camel_case($model));
        $methodName = camel_case($methodName);
        return "{$model}@{$methodName}";
    }

    /**
     * @param $id
     * @param $roleIds
     * @internal Permission $permission
     */
    public function syncRoles($id, $roleIds)
    {
        $permission = $this->find($id);
        $roles = Role::whereIn('id', $roleIds)->get();
        $permission->syncRoles($roles);
    }

    /**
     * @no-permission
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes)
    {
        $permission = parent::create($attributes);
        if ($permission->is_default_action === false) {
            $this->setPermissionLang($permission->id, \App::getLocale(), $attributes['label']);
            $permission->setTempLabel($attributes['label']);
        }
        return $permission;
    }

    /**
     * @no-permission
     * @param array $attributes
     * @param $id
     * @return mixed
     */
    public function update(array $attributes, $id)
    {
        $permission = parent::update($attributes, $id);
        if (isset($attributes['label']) && $permission->is_default_action === false) {
            $this->setPermissionLang($id, \App::getLocale(), $attributes['label']);
            $permission->setTempLabel($attributes['label']);
        }

        return $permission;
    }

    /**
     * @param $permission
     * @param $locale
     * @param $text
     * @throws FileNotFoundException
     */
    private function setPermissionLang($permission, $locale, $text)
    {
        if (is_numeric($permission)) {
            $permission = $this->find($permission);
        }

        if (!$permission) {
            return;
        }

        $model = snake_case($permission->model);
        $action = snake_case($permission->action);
        $languageFilePath = resource_path("lang/{$locale}/{$model}.php");
        if (!file_exists($languageFilePath)) {
            throw new FileNotFoundException("resources/lang/{$locale}/{$model}.php doesn't exist.");
        }
        $languageFileContent = file_get_contents($languageFilePath);

        $text = addslashes($text);
        $regexp = "/('permissions'.*=>[\w\W]*)('{$action}'\s*=>\s*')(.+)'/";

        if (preg_match($regexp, $languageFileContent) === 1) {
            $replaceExp = "$1$2{$text}'";
            $languageFileContent = preg_replace($regexp, $replaceExp, $languageFileContent);
        } else {
            $newLangArray = require $languageFilePath;
            array_set(
                $newLangArray, "permissions.{$action}",
                $text
            );
            $languageFileContent = "<?php\n\nreturn " . Utils::exportArray($newLangArray) . ";\n";
        }

        file_put_contents($languageFilePath, $languageFileContent);
    }
}
