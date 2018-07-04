<?php namespace Tests\Modules\User\Role;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Modules\User\Models\Permission;
use Modules\User\Models\Role;
use Modules\User\Repositories\RoleRepository;
use Tests\TestCase;

class CopyTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * @var Role
     */
    private $tempRole = null;
    /**
     * @var Collection
     */
    private $tempPermissions = [];

    public function setUp()
    {
        parent::setUp();

        $this->tempRole = factory(Role::class)->create();
        $this->tempPermissions = factory(Permission::class)->times(10)->create();
        $this->tempRole->syncPermissions($this->tempPermissions->slice(0, 5));
    }

    public function testCopySuccessfully()
    {
        // Reference: https://laravel.com/docs/5.5/http-tests
        $user = $this->getSuperAdmin();
        $request = $this->actingAs($user, 'api');

        $newRoleName = "new-role-" . rand(1, 1000);
        $response = $request->post('api/user/role/copy', ['id' => $this->tempRole->id, 'name' => $newRoleName]);
        $response->assertStatus(200);

        $newRole = Role::where('name', $newRoleName)->first();
        $this->assertEquals($newRoleName, $newRole->name);
        $this->assertEquals($this->tempRole->guard_name, $newRole->guard_name);
        $this->assertEquals(5, count($newRole->permissions));
        $this->assertEquals(
            $this->getPermissionIds($this->tempRole->permissions),
            $this->getPermissionIds($newRole->permissions));
    }

    public function testCopyWithError()
    {
        // Reference: https://laravel.com/docs/5.5/http-tests
        $user = $this->getSuperAdmin();
        $request = $this->actingAs($user, 'api');
        $response = $this->post('api/user/role/copy', ['id' => $this->tempRole->id]);

        $response->assertStatus(422);

        $response = $this->post('api/user/role/copy', ['name' => 'some-new-role-name-' . rand(1, 1000)]);

        $response->assertStatus(422);
    }

    private function getPermissionIds($permissions)
    {
        $ids = $permissions->map(function ($permission) {
            return $permission->id;
        })->toArray();
        sort($ids);
        return $ids;
    }

    private function getSuperAdmin()
    {
        return RoleRepository::GET_SUPER_ADMIN()->users()->first();
    }
}
