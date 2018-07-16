<?php namespace Modules\User\Observers;

use App\Scaffold\Installer\Utils;
use Modules\User\Models\Permission;
use Modules\User\Repositories\RoleRepository;

class PermissionObserver
{
    public function deleted(Permission $permission)
    {
        if ($permission->is_default_action === false) {
            $this->removeLanguageTerm($permission->model, $permission->action);
        }
    }

    /**
     * Listen to the Role created event.
     *
     * @param Permission $permission
     * @return void
     */
    public function updated(Permission $permission)
    {
        $original = $permission->getOriginal();
        if (count($original)) {
            $oldPermission = new Permission($original);
            if ($permission->name != $oldPermission->name) {
                $this->updateLanguageTerm($oldPermission, $permission);
            }
        } elseif ($permission->is_default_action === false) {
            $this->createLanguageTerm($permission->model, $permission->action);
        }
    }

    /**
     * Listen to the Permission created event.
     *
     * @param Permission $permission
     * @return void
     */
    public function created(Permission $permission)
    {
        $superAdminRole = RoleRepository::GET_SUPER_ADMIN();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permission);
        }
        //
        if ($permission->is_default_action === false) {
            $this->createLanguageTerm($permission->model, $permission->action);
        }
    }

    private function updateLanguageTerm(Permission $oldPermission, Permission $newPermission)
    {
        $oldModel = snake_case($oldPermission->model);
        $oldAction = snake_case($oldPermission->action);
        $newModel = snake_case($newPermission->model);
        $newAction = snake_case($newPermission->action);
        $folder = resource_path("lang/*/");
        foreach (glob($folder) as $langFolder) {
            $oldLangFilePath = $langFolder . $oldModel . '.php';
            $oldLangArray = require $oldLangFilePath;

            $langText = $newPermission->name;
            if (array_has($oldLangArray, "permissions.{$oldAction}")) {
                $langText = $oldLangArray['permissions'][$oldAction];
                //  Remove old permission lang terms
                unset($oldLangArray['permissions'][$oldAction]);
                $oldLangContent = "<?php\n\nreturn " . Utils::exportArray($oldLangArray) . ";\n";
                file_put_contents($oldLangFilePath, $oldLangContent);
            }

            // Add new permission lang terms
            $newLangFilePath = $langFolder . $newModel . '.php';
            $newLangArray = require $newLangFilePath;
            array_set(
                $newLangArray, "permissions.{$newAction}",
                $langText
            );
            $newLangContent = "<?php\n\nreturn " . Utils::exportArray($newLangArray) . ";\n";
            file_put_contents($newLangFilePath, $newLangContent);
        }
    }

    private function createLanguageTerm($modelName, $actionName)
    {
        $modelName = snake_case($modelName);
        $actionName = snake_case($actionName);

        $folder = resource_path("lang/*/{$modelName}.php");
        foreach (glob($folder) as $langFile) {
            $langFileContent = require $langFile;
            if (!isset($langFileContent['permissions'])) {
                $langFileContent['permissions'] = [];
            }
            if (!isset($langFileContent['permissions'][$actionName])) {
                $langFileContent['permissions'][$actionName] = $actionName;
                $langContent = "<?php\n\nreturn " . Utils::exportArray($langFileContent) . ";\n";
                file_put_contents($langFile, $langContent);
            }
        }
    }

    private function removeLanguageTerm($modelName, $actionName)
    {
        $modelName = snake_case($modelName);
        $actionName = snake_case($actionName);

        $folder = resource_path("lang/*/{$modelName}.php");
        foreach (glob($folder) as $langFile) {
            $langFileContent = require $langFile;
            if (!isset($langFileContent['permissions'])) {
                continue;
            }
            if (isset($langFileContent['permissions'][$actionName])) {
                unset($langFileContent['permissions'][$actionName]);
                $langFileContent = "<?php\n\nreturn " . Utils::exportArray($langFileContent) . ";\n";
                file_put_contents($langFile, $langFileContent);
            }
        }
    }
}
