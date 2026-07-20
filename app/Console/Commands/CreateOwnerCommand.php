<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateOwnerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-owner';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membuat akun owner pertama untuk aplikasi Laporan Keuangan';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = \Laravel\Prompts\text('Nama Owner:', default: 'Owner Toko');
        $email = \Laravel\Prompts\text(
            label: 'Email Owner:',
            required: true,
            validate: fn (string $value) => !filter_var($value, FILTER_VALIDATE_EMAIL) ? 'Format email tidak valid.' : null
        );
        
        if (User::where('email', $email)->exists()) {
            $this->error('User dengan email tersebut sudah ada.');
            return self::FAILURE;
        }

        $password = \Laravel\Prompts\password('Password (minimal 8 karakter):', required: true);

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'owner',
        ]);

        $this->info('Akun owner berhasil dibuat! Silakan masuk melalui halaman login.');
        return self::SUCCESS;
    }
}
