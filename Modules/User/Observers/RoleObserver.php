<?php namespace Modules\User\Observers;

use App\Scaffold\Installer\Utils;
use Modules\User\Models\Role;

class RoleObserver
{
    /**
     * Listen to the Role created event.
     *
     * @param Role $role
     * @return void
     */
    public function updated(Role $role)
    {
        $oldRole = new Role($role->getOriginal());
        $oldRoleName = $oldRole->snake_case_name;
        $newRoleName = $role->snake_case_name;
        if ($newRoleName != $oldRoleName) {
            $this->updateLanguageTerm($oldRoleName, $newRoleName);
        }
    }

    /**
     * Listen to the Role created event.
     *
     * @param Role $role
     * @return void
     */
    public function created(Role $role)
    {
        $this->createLanguageTerm($role);
    }

    /**
     * To update language files in case of the role name is changed.
     * @param $oldRoleName
     * @param $newRoleName
     */
    private function updateLanguageTerm($oldRoleName, $newRoleName)
    {
        $folder = resource_path("lang/*/role.php");
        foreach (glob($folder) as $langFile) {
            $langFileContent = require $langFile;
            if (!isset($langFileContent['roles'])) {
                $langFileContent['roles'] = [];
            }
            if (isset($langFileContent['roles'][$oldRoleName])) {
                $langFileContent['roles'][$newRoleName] = $langFileContent['roles'][$oldRoleName];
                unset($langFileContent['roles'][$oldRoleName]);
                $langContent = "<?php\n\nreturn " . Utils::exportArray($langFileContent) . ";\n";
                file_put_contents($langFile, $langContent);
            }
        }
    }

    private function createLanguageTerm(Role $role)
    {
        $roleName = $role->snake_case_name;

        $folder = resource_path("lang/*/role.php");
        foreach (glob($folder) as $langFile) {
            $langFileContent = require $langFile;
            if (!isset($langFileContent['roles'])) {
                $langFileContent['roles'] = [];
            }
            if (!isset($langFileContent['roles'][$roleName])) {
                $langFileContent['roles'][$roleName] = studly_case($roleName);
                $langContent = "<?php\n\nreturn " . Utils::exportArray($langFileContent) . ";\n";
                file_put_contents($langFile, $langContent);
            }
        }
    }
}
