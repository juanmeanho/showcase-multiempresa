<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateSuperAdmin extends Command
{
    protected $signature = 'app:create-super-admin';

    protected $description = 'Crea la cuenta superadministradora de la plataforma';

    public function handle(): int
    {
        if (User::where('is_super_admin', true)->exists()) {
            $this->error('Ya existe una cuenta superadministradora.');
            $this->line('Por seguridad, no se creará otra cuenta.');

            return self::FAILURE;
        }

        $name = trim((string) $this->ask('Nombre del superadministrador'));
        $email = strtolower(trim((string) $this->ask('Correo electrónico')));

        if ($name === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Debes indicar un nombre y un correo válido.');

            return self::FAILURE;
        }

        if (User::where('email', $email)->exists()) {
            $this->error('Ya existe un usuario con ese correo electrónico.');

            return self::FAILURE;
        }

        $password = (string) $this->secret('Contraseña (mínimo 12 caracteres)');
        $confirmation = (string) $this->secret('Confirma la contraseña');

        if (strlen($password) < 12 || $password !== $confirmation) {
            $this->error('Las contraseñas no coinciden o tienen menos de 12 caracteres.');

            return self::FAILURE;
        }

        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = $password;
        $user->is_super_admin = true;
        $user->save();

        $this->info('Cuenta superadministradora creada correctamente.');
        $this->line('Correo: '.$user->email);
        $this->line('Ya puedes iniciar sesión en /login.');

        return self::SUCCESS;
    }
}
