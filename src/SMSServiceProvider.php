<?php

namespace Jundayw\SMS;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Jundayw\SMS\Contracts\SMSManagerContract;
use Jundayw\SMS\Events\SMSSent;
use Jundayw\SMS\Listeners\SMSSentListener;

class SMSServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/sms.php', 'sms');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/sms.php' => config_path('sms.php'),
            ], 'laravel-sms-config');
        }

        Event::listen(SMSSent::class, SMSSentListener::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->app->singleton(SMSManagerContract::class, function () {
            return new SMSManager($this->app['config']['sms.default']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [SMSManagerContract::class];
    }
}
