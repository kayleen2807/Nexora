<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserInvitation;
use App\Notifications\UserInvitationNotification;
use App\Services\UserInvitationService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserInvitationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('user_invitations');
        Schema::dropIfExists('usuario');

        Schema::create('usuario', function (Blueprint $table) {
            $table->increments('id_usuario');
            $table->string('nombre');
            $table->string('apellido');
            $table->string('correo')->unique();
            $table->string('contrasena');
            $table->unsignedInteger('id_rol');
            $table->unsignedInteger('id_sucursal')->nullable();
            $table->timestamp('email_verified_at')->nullable();
        });

        Schema::create('user_invitations', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('invited_by');
            $table->string('token_hash');
            $table->timestamp('expires_at');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->integer('accepted_by')->nullable();
            $table->string('status', 20)->default('sent');
            $table->string('terms_version', 50)->nullable();
            $table->string('privacy_version', 50)->nullable();
            $table->ipAddress('accepted_ip')->nullable();
            $table->timestamps();
        });
    }

    public function test_invitation_stores_a_hashed_token_and_sends_a_notification(): void
    {
        Notification::fake();
        $admin = $this->createUser('admin@example.test');
        $user = $this->createUser('user@example.test');

        $invitation = app(UserInvitationService::class)->send($user, $admin);

        $this->assertSame('sent', $invitation->status);
        $this->assertTrue($invitation->expires_at->equalTo(now()->addMinutes(5)->startOfMinute()) || $invitation->expires_at->greaterThan(now()->addMinutes(4)));
        $this->assertFalse(Hash::check('not-the-token', $invitation->token_hash));
        Notification::assertSentTo($user, UserInvitationNotification::class);
    }

    public function test_valid_invitation_acceptance_sets_password_and_consumes_token(): void
    {
        Notification::fake();
        $admin = $this->createUser('admin@example.test');
        $user = $this->createUser('user@example.test');
        $plainToken = 'temporary-token-for-test';
        $invitation = UserInvitation::create([
            'user_id' => $user->id_usuario,
            'invited_by' => $admin->id_usuario,
            'token_hash' => Hash::make($plainToken),
            'expires_at' => now()->addMinutes(5),
            'sent_at' => now(),
            'status' => 'sent',
        ]);

        $response = $this->post(route('invitation.accept', [$invitation, $plainToken]), [
            'password' => 'NuevaClave123!',
            'password_confirmation' => 'NuevaClave123!',
            'acepta_terminos' => '1',
            'acepta_privacidad' => '1',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertSame('accepted', $invitation->fresh()->status);
        $this->assertNotNull($invitation->fresh()->accepted_at);
        $this->assertTrue(Hash::check('NuevaClave123!', $user->fresh()->contrasena));
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    private function createUser(string $correo): User
    {
        return User::create([
            'nombre' => 'Test',
            'apellido' => 'User',
            'correo' => $correo,
            'contrasena' => 'Original123!',
            'id_rol' => 1,
        ]);
    }
}