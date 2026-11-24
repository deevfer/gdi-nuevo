<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

/**
 * Asigna el único super admin del panel. Si el correo no existe, crea el usuario.
 * Cualquier otro super admin pasa a webmaster.
 */
class MakeSuperAdmin extends Command
{
    protected $signature = 'gdi:super-admin {email : Correo del super admin}';

    protected $description = 'Crea o asigna el único super admin del CMS';

    public function handle(): int
    {
        $email = mb_strtolower(trim($this->argument('email')));
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('El correo no es válido.');

            return self::FAILURE;
        }

        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            $user = new User([
                'email' => $email,
                'name' => text('Nombre', required: true),
                'password' => password('Contraseña (mínimo 8 caracteres)', required: true, validate: fn (string $value): ?string => mb_strlen($value) < 8 ? 'Use al menos 8 caracteres.' : null),
            ]);
        }

        DB::transaction(function () use ($user): void {
            User::query()->where('role', UserRole::SuperAdmin)->whereKeyNot($user->getKey())->update(['role' => UserRole::Webmaster]);
            $user->role = UserRole::SuperAdmin;
            $user->save();
        });

        $this->info("{$user->email} es ahora el super admin del CMS.");

        return self::SUCCESS;
    }
}
