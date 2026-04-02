<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class UserPasswordCommand extends Command
{
    protected $signature = 'user:password {email : Email пользователя} {password : Новый пароль}';
    protected $description = 'Сменить пароль пользователя по email (для админа на продакшене)';

    public function handle(): int
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("Пользователь с email {$email} не найден.");
            return 1;
        }

        $user->password = $password;
        $user->save();

        $this->info("Пароль для {$email} успешно изменён.");
        return 0;
    }
}
