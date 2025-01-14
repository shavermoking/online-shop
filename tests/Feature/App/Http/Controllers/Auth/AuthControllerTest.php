<?php

namespace Feature\App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\SignInController;
use App\Http\Controllers\Auth\SignUpController;
use App\Listeners\SendEmailNewUserListener;
use App\Notifications\NewUserNotification;
use Database\Factories\UserFactory;
use Domain\Auth\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\RequestFactories\SignInFormRequestFactory;
use Tests\RequestFactories\SignUpFormRequestFactory;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;


    public function test_login_page_success(): void
    {
        $this->get(action([SignInController::class, 'page']))
            ->assertOk()
            ->assertSee('Вход в аккаунт')
            ->assertViewIs('auth.login');
    }

    public function test_sign_up_page_success(): void
    {
        $this->get(action([SignUpController::class, 'page']))
            ->assertOk()
            ->assertSee('Регистрация')
            ->assertViewIs('auth.sign-up');
    }

    public function test_forgot_page_success(): void
    {
        $this->get(action([ForgotPasswordController::class, 'page']))
            ->assertOk()
            ->assertViewIs('auth.forgot-password');
    }

    public function test_is_sign_in_success(): void
    {
        $password = '123456789';
        $user = UserFactory::new()->create([
            'email' => 'nikita@mail.ru',
            'password' => bcrypt($password)
        ]);
        $request = SignInFormRequestFactory::new()->create([
            'email' => $user->email,
            'password' => $password
        ]);

        $response = $this->post(action([SignInController::class, 'handle']), $request);

        $response->assertValid()
            ->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_is_logout_success(): void
    {
        $user = Userfactory::new()->create([
            'email' => 'nikita@mail.ru'
        ]);

        $this->actingAs($user)
            ->delete(action([SignInController::class, 'logOut']));

        $this->assertGuest();
    }



    public function test_is_sign_up_success(): void
    {
        Notification::fake();
        Event::fake();

        $request = SignUpFormRequestFactory::new()->create([
            'email' => 'nikita@mail.ru',
            'password' => '12345678',
            'password_confirmation' => '12345678'
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => $request['email']
        ]);

        $response = $this->post(
            action([SignUpController::class, 'handle']),
            $request
        );

        $response->assertValid();

        $this->assertDatabaseHas('users', [
            'email' => $request['email']
        ]);

        $user = User::query()
            ->where('email', $request['email'])
            ->first();

        Event::assertDispatched(Registered::class);
        Event::assertListening(Registered::class, SendEmailNewUserListener::class);

        $event = new Registered($user);
        $listener = new SendEmailNewUserListener();
        $listener->handle($event);

        $this->assertAuthenticatedAs($user);

        Notification::assertSentTo($user, NewUserNotification::class);
        $response->assertRedirect(route('home'));
    }
}







