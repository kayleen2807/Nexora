<?php

namespace App\Providers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // En producción todos los enlaces que genera Laravel van con https://
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Correo por la API HTTP de Brevo (MAIL_MAILER=brevo). Railway bloquea el puerto SMTP
        // en sus planes Trial/Free/Hobby; la API usa HTTPS normal y sí pasa.
        Mail::extend('brevo', function () {
            return (new BrevoTransportFactory)->create(
                new Dsn('brevo+api', 'default', config('services.brevo.key'))
            );
        });
    }
}
