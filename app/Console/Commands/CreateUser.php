<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-user {username} {password} {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user with username, password, and email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $username = $this->argument('username');
        $password = $this->argument('password');
        $email = $this->argument('email');

        $user = User::create([
            'name' => $username,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        if ($user) {
            $this->info('User successfully created.');
        } else {
            $this->error('Failed to create user.');
        }
    }
}
