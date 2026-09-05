<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    // C'est le nom de la commande que tu taperasi dans le terminal
    protected $signature = 'make:admin';

    protected $description = 'Créer un compte administrateur initial pour le Marketplace';

    public function handle()
    {
        $defaultUsers = [
            [
                'name' => 'Administrateur EPF',
                'email' => 'admin@epf.sn',
                'password' => Hash::make('AdminEpf2026!'),
                'role' => 'admin',
                'bio' => 'Compte administrateur principal du Marketplace.',
            ],
            [
                'name' => 'Vendeur EPF',
                'email' => 'vendeur@epf.sn',
                'password' => Hash::make('VendeurEpf2026!'),
                'role' => 'seller',
                'bio' => 'Boutique officielle Vendeur EPF.',
                'city' => 'Dakar',
                'phone' => '+221770000000',
            ],
            [
                'name' => 'Acheteur EPF',
                'email' => 'acheteur@epf.sn',
                'password' => Hash::make('AcheteurEpf2026!'),
                'role' => 'buyer',
                'bio' => 'Client officiel EPF.',
                'city' => 'Dakar',
                'phone' => '+221780000000',
            ],
            [
                'name' => 'Seller Demo',
                'email' => 'seller@example.com',
                'password' => Hash::make('secret12'),
                'role' => 'seller',
                'bio' => 'Boutique de démonstration.',
            ],
            [
                'name' => 'Buyer Demo',
                'email' => 'buyer@example.com',
                'password' => Hash::make('secret12'),
                'role' => 'buyer',
                'bio' => 'Client démo.',
            ],
        ];

        foreach ($defaultUsers as $userData) {
            $user = User::where('email', $userData['email'])->first();
            if ($user) {
                $user->update([
                    'name' => $userData['name'],
                    'role' => $userData['role'],
                    'password' => $userData['password'],
                    'bio' => $userData['bio'] ?? $user->bio,
                    'city' => $userData['city'] ?? $user->city,
                    'phone' => $userData['phone'] ?? $user->phone,
                ]);
                $this->info("Compte mis à jour : {$userData['email']} ({$userData['role']})");
            } else {
                User::create($userData);
                $this->info("Compte créé avec succès : {$userData['email']} ({$userData['role']})");
            }
        }

        return Command::SUCCESS;
    }
}