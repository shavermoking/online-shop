<?php

namespace Domain\Catalog\Providers;

// use Illuminate\Support\Facades\Gate;
use Domain\Auth\Actions\RegisterNewUserAction;
use Domain\Auth\Contracts\RegisterNewUserContract;
use Illuminate\Support\ServiceProvider;

class ActionsServiceProvider extends ServiceProvider
{

    public array $bindings = [
        RegisterNewUserContract::class => RegisterNewUserAction::class
    ];

}
