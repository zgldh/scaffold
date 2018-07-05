<?php namespace Tests\Modules\User\Permission;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Modules\User\Models\Permission;
use Modules\User\Models\Role;
use Tests\TestCase;
use Modules\User\Models\User;

class SyncRolesTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var Role
     */
    private $tempPermission = null;
    /**
     * @var Collection
     */
    private $tempRoles = [];

    public function setUp()
    {
        parent::setUp();

        $this->tempPermission = factory(Permission::class)->create();
        $this->tempRoles = factory(Role::class)->times(10)->create();
        $this->tempPermission->syncRoles($this->tempRoles->slice(0, 5));
    }

    public function testSyncRolesSuccessfully()
    {
        // Reference: https://laravel.com/docs/5.5/http-tests
        $user = factory(User::class)->create();
        $request = $this->actingAs($user, 'api');

        $syncRoles = $this->tempRoles->slice(3, 6);
        $data = [
            'roleIds' => $this->getRoleIds($syncRoles)
        ];
        $response = $request->put('api/user/permission/' . $this->tempPermission->id . '/sync_roles', $data);
        $response->assertStatus(200);

        $newRoles = $this->tempPermission->roles()->get();
        $this->assertEquals(6, count($newRoles));
        $this->assertEquals($this->getRoleIds($syncRoles), $this->getRoleIds($newRoles));
    }

    public function testSyncRolesWithError()
    {
        // Reference: https://laravel.com/docs/5.5/http-tests
        $response = $this->put('api/user/permission/{id}/sync_roles');
        $response->assertStatus(500);
    }

    private function getRoleIds(Collection $roles)
    {
        $ids = $roles->map(function ($role) {
            return $role->id;
        })->toArray();
        sort($ids);
        return $ids;
    }
}
