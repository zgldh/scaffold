<?php namespace Tests\Modules\Setting\Setting;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Modules\Setting\Repositories\SettingRepository;
use Tests\TestCase;
use Modules\User\Models\User;

class SettingUpdateTest extends TestCase
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

    public function testSettingUpdateSuccessfully()
    {
        // Reference: https://laravel.com/docs/5.5/http-tests
        $user = factory(User::class)->create();
        $user->givePermissionTo('Setting@update');
        $user->givePermissionTo('Setting@update');
        $request = $this->actingAs($user, 'api');

        $repository = $this->app->make(SettingRepository::class);

        $settingKey = 'site_name';
        $settingValue = 'value-' . str_random();

        $response = $request->put('api/setting/system', [
            'name'  => $settingKey,
            'value' => $settingValue
        ]);
        $response->assertStatus(200);

        $itemValue = $repository->getItemValue($settingKey);
        $this->assertEquals($itemValue, $settingValue);
    }

    public function testSettingUpdateWithError()
    {
        // Reference: https://laravel.com/docs/5.5/http-tests
        $response = $this->put('api/setting/{id}');
        $response->assertStatus(500);
    }
}
