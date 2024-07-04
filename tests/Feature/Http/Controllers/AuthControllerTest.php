<?php

namespace Tests\Feature\Http\Controllers;

use App\Listeners\SendEmailNewUserListener;
use App\Models\User;
use App\Notifications\NewUserNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_store_success(): void
    {
        Event::fake();
        Notification::fake();

        $request = [
            'name' => 'Ryan Gosling',
            'email' => 'ryan@yandex.ru',
            'password' => '12345678',
            'password_confirmation' => '12345678'
        ];

        $token = csrf_token();

        $response = $this->withoutMiddleware()
            ->post(route('store'), $request);

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => $request['email']
        ]);

        $user = User::query()->where(['email' => $request['email']])->first();

        Event::assertDispatched(Registered::class);
        Event::assertListening(Registered::class, SendEmailNewUserListener::class);

        $event = new Registered($user);
        $listener = new SendEmailNewUserListener();
        $listener->handle($event);

        Notification::assertSentTo($user, NewUserNotification::class);

        $response->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
    }
}
