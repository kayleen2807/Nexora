<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('usuario');

        Schema::create('usuario', function (Blueprint $table) {
            $table->increments('id_usuario');
            $table->string('nombre');
            $table->string('apellido');
            $table->date('fecha_nacimiento')->nullable();
            $table->string('correo')->unique();
            $table->string('contrasena');
            $table->string('remember_token')->nullable();
            $table->unsignedInteger('id_rol');
            $table->unsignedInteger('id_sucursal')->nullable();
            $table->string('idioma')->default('es');
        });

        $migration = require database_path('migrations/2026_10_11_000000_create_password_reset_tokens_table.php');
        $migration->up();
    }

    public function test_existing_user_receives_a_reset_notification_and_generic_response(): void
    {
        Notification::fake();
        $user = $this->createUser();

        $response = $this->post(route('password.email'), [
            'correo' => $user->correo,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', __('If an account uses that email, we will send a password reset link.'));
        Notification::assertSentTo($user, ResetPasswordNotification::class, function (ResetPasswordNotification $notification) use ($user): bool {
            App::setLocale('es');
            $message = $notification->toMail($user);

            $this->assertSame('Restablece tu contraseña', $message->subject);
            $this->assertSame(
                route('password.reset', ['token' => $notification->token, 'email' => $user->correo]),
                $message->viewData['url']
            );
            $this->assertSame(asset('img/nexora-logo-cropped.png'), $message->viewData['logoUrl']);

            $html = view($message->view['html'], $message->viewData)->render();
            $this->assertStringContainsString($message->viewData['logoUrl'], $html);

            return true;
        });
    }

    public function test_unknown_email_gets_the_same_generic_response_without_notification(): void
    {
        Notification::fake();

        $response = $this->post(route('password.email'), [
            'correo' => 'unknown@example.test',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', __('If an account uses that email, we will send a password reset link.'));
        Notification::assertNothingSent();
    }

    public function test_valid_reset_token_changes_the_hashed_password_and_is_consumed(): void
    {
        Notification::fake();
        $user = $this->createUser();
        $token = null;

        $this->post(route('password.email'), ['correo' => $user->correo]);

        Notification::assertSentTo($user, ResetPasswordNotification::class, function (ResetPasswordNotification $notification) use (&$token): bool {
            $token = $notification->token;

            return true;
        });

        $this->assertNotNull($token);

        $response = $this->post(route('password.update'), [
            'correo' => $user->correo,
            'token' => $token,
            'password' => 'NuevaClave123!',
            'password_confirmation' => 'NuevaClave123!',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status', __('Your password has been reset. Please log in.'));
        $this->assertTrue(Hash::check('NuevaClave123!', $user->fresh()->contrasena));
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->correo]);
    }

    public function test_invalid_reset_token_is_rejected(): void
    {
        App::setLocale('en');
        $user = $this->createUser();
        $resetUrl = route('password.reset', ['token' => 'invalid-token', 'email' => $user->correo]);

        $response = $this->from($resetUrl)->post(route('password.update'), [
            'correo' => $user->correo,
            'token' => 'invalid-token',
            'password' => 'NuevaClave123!',
            'password_confirmation' => 'NuevaClave123!',
        ]);

        $response->assertRedirect($resetUrl);
        $response->assertSessionHasErrors([
            'correo' => __('The reset link is invalid or has expired.'),
        ]);
    }

    public function test_recovery_views_follow_the_selected_locale(): void
    {
        $this->withSession(['locale' => 'es'])
            ->get(route('password.request'))
            ->assertOk()
            ->assertSee('lang="es"', false)
            ->assertSee('Enviar enlace de recuperación');

        $this->withSession(['locale' => 'en'])
            ->get(route('password.reset', [
                'token' => 'sample-token',
                'email' => 'test@example.test',
            ]))
            ->assertOk()
            ->assertSee('lang="en"', false)
            ->assertSee('Set a new password');
    }

    public function test_remember_token_migration_adds_a_missing_column_idempotently(): void
    {
        Schema::table('usuario', function (Blueprint $table) {
            $table->dropColumn('remember_token');
        });

        $migration = require database_path('migrations/2026_10_11_000001_add_remember_token_to_usuario_table.php');
        $migration->up();
        $migration->up();
        $migration->down();

        $this->assertTrue(Schema::hasColumn('usuario', 'remember_token'));
    }

    private function createUser(): User
    {
        return User::create([
            'nombre' => 'Test',
            'apellido' => 'User',
            'correo' => 'test@example.test',
            'contrasena' => 'Original123!',
            'id_rol' => 1,
            'id_sucursal' => null,
            'idioma' => 'es',
        ]);
    }
}