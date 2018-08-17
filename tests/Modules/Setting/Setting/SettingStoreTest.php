<?php namespace Tests\Modules\Setting\Setting;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Modules\User\Models\User;

class SettingStoreTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

//      Prepare testing database environment.
        /**
         * $this->createTestUser();
         **/
    }

    public function testSettingStoreSuccessfully()
    {
        // Reference: https://laravel.com/docs/5.5/http-tests
        $user = factory(User::class)->create();
        $request = $this->actingAs($user, 'api');

        $response = $request->post('api/setting');
        $response->assertStatus(200);
    }

    public function testSettingStoreWithError()
    {
        // Reference: https://laravel.com/docs/5.5/http-tests
        $response = $this->post('api/setting');
        $response->assertStatus(500);
    }
}
