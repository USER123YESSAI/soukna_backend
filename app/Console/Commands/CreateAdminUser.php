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
        $email = 'admin@epf.sn'; 
        
    
        if (User::where('email', $email)->exists()) {
            $this->info('Un administrateur avec cet email existe déjà : aucun changement effectué.');
            return Command::SUCCESS;
        }


        // Création de l'administrateur
        $admin = User::create([
            'name' => 'Administrateur EPF',
            'email' => $email,
            'password' => Hash::make('AdminEpf2026!'), 
            'role' => 'admin', 
            'bio' => 'Compte administrateur principal du Marketplace.',
        ]);

        $this->info('Compte administrateur créé avec succès !');
        $this->info('Email : ' . $email);
        
        return Command::SUCCESS;
    }
}