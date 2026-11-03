<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\EmailVerificationService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('usuario');
        Schema::create('usuario', function (Blueprint $table) {
            $table->increments('id_usuario');
            $table->string('nombre');
            $table->string('apellido');
            $table->string('correo')->unique();
            $table->string('contrasena');
            $table->unsignedInteger('id_rol');
            $table->unsignedInteger('id_sucursal')->nullable();
            $table->string('idioma')->default('es');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('email_verification_code')->nullable();
            $table->timestamp('email_verification_expires_at')->nullable();
        });
    }

    public function test_service_sends_a_six_digit_code_and_stores_only_its_hash(): void
    {
        Notification::fake();
        $user = $this->createUser();

        app(EmailVerificationService::class)->send($user);
        $user->refresh();

        $this->assertMatchesRegularExpression('/^\$2y\$/', $user->email_verification_code);
        $this->assertNotNull($user->email_verification_expires_at);
        Notification::assertSentTo($user, \App\Notifications\EmailVerificationCodeNotification::class);
    }

    public function test_valid_code_marks_user_as_verified_and_clears_otp(): void
    {
        Notification::fake();
        $user = $this->createUser();
        $code = '123456';
        $user->forceFill([
            'email_verification_code' => Hash::make($code),
            'email_verification_expires_at' => now()->addMinutes(10),
        ])->save();

        $response = $this->post(route('verification.verify'), [
            'correo' => $user->correo,
            'codigo' => $code,
        ]);

        $response->assertRedirect(route('login'));
        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->assertNull($user->fresh()->email_verification_code);
    }

    public function test_expired_code_is_rejected(): void
    {
        $user = $this->createUser();
        $user->forceFill([
            'email_verification_code' => Hash::make('123456'),
            'email_verification_expires_at' => now()->subMinute(),
        ])->save();

        $response = $this->from(route('verification.notice', ['correo' => $user->correo]))
            ->post(route('verification.verify'), [
                'correo' => $user->correo,
                'codigo' => '123456',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('codigo');
        $this->assertNull($user->fresh()->email_verified_at);
    }

    private function createUser(): User
    {
        return User::create([
            'nombre' => 'Test',
            'apellido' => 'User',
            'correo' => 'verify@example.test',
            'contrasena' => 'Original123!',
            'id_rol' => 1,
        ]);
    }
}