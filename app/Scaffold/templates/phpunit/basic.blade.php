<?php
/**
 */
echo '<?php' ?> namespace Tests\Modules\{{$moduleNameSpace}};

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Modules\User\Models\User;

class {{$testClassName}} extends TestCase
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

    public function test{{$testFunctionName}}Successfully()
    {
        // Reference: https://laravel.com/docs/5.5/http-tests
        $user = factory(User::class)->create();
        $request = $this->actingAs($user, 'api');

        $response = $request->{{$method}}('{{$route}}');
        $response->assertStatus(200);
    }

    public function test{{$testFunctionName}}WithError()
    {
        // Reference: https://laravel.com/docs/5.5/http-tests
        $response = $this->{{$method}}('{{$route}}');
        $response->assertStatus(500);
    }
}
