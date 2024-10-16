<?php

namespace Feature\Auth\Actions;

use Domain\Auth\Contracts\RegisterNewUserContract;
use Domain\Auth\DTOs\NewUserDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterNewUserActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_success_user_created(): void
    {
        $this->assertDatabaseMissing('users', [
            'email' => 'test@mail.ru'
        ]);

        $action = app(RegisterNewUserContract::class);

        $action(NewUserDTO::make('Test', 'test@mail.ru', '1234567890'));

        $this->assertDatabaseHas('users', [
            'email' => 'test@mail.ru'
        ]);
    }
}
