<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateAdministrator extends Command
{
    protected $signature = 'app:create-admin {email} {--name=Administrator}';

    protected $description = 'Create an administrator with a securely prompted password';

    public function handle(): int
    {
        if (! $this->input->isInteractive()) {
            $this->error('Run interactively to enter the password securely.');

            return self::FAILURE;
        }
        $data = ['email' => $this->argument('email'), 'name' => $this->option('name'), 'password' => $this->secret('Password (at least 8 characters)')];
        $validator = Validator::make($data, ['email' => ['required', 'email', 'unique:users,email'], 'name' => ['required', 'string', 'max:255'], 'password' => ['required', 'string', 'min:8']]);
        if ($validator->fails()) {
            $this->error($validator->errors()->first());

            return self::FAILURE;
        }
        User::create([...$data, 'role' => 'admin', 'is_active' => true]);
        $this->info('Administrator created. Sign in at /admin.');

        return self::SUCCESS;
    }
}
