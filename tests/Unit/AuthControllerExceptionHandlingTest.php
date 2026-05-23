<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthControllerExceptionHandlingTest extends TestCase
{
    /**
     * Test that AuthController::login catches RuntimeExceptions (such as Bcrypt hasher exceptions)
     * and redirects the user back with an error message instead of crashing the site.
     */
    public function test_login_catches_runtime_exception_and_redirects_back_with_error(): void
    {
        // Mock Auth::shouldReceive('attempt') to throw RuntimeException
        Auth::shouldReceive('attempt')
            ->once()
            ->with(['username' => 'testuser', 'password' => 'somepassword'])
            ->andThrow(new \RuntimeException('This password does not use the Bcrypt algorithm.'));

        // Create request
        $request = Request::create('/auth/login', 'POST', [
            'username' => 'testuser',
            'password' => 'somepassword',
        ]);
        
        // Set session on the request
        $request->setLaravelSession($this->app['session']->driver('array'));

        $controller = new AuthController();
        $response = $controller->login($request);

        // The response should be a redirect back (redirect status code is 302)
        $this->assertEquals(302, $response->getStatusCode());
        
        // Assert the session has the error message
        $this->assertEquals('Username atau password salah!', session('error'));
    }
}
