<?php
namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->truncate();

        $company = Company::first();

        $travelers = [
            ['name' => 'Isabela Carvalho', 'email' => 'isabela.carvalho@meridian.com.br', 'department' => 'Engenharia', 'position' => 'Engenheira Civil Sênior'],
            ['name' => 'Rafael Mendonça', 'email' => 'rafael.mendonca@meridian.com.br', 'department' => 'Comercial', 'position' => 'Gerente de Contas'],
            ['name' => 'Priscila Souza', 'email' => 'priscila.souza@meridian.com.br', 'department' => 'Projetos', 'position' => 'Coordenadora de Projetos'],
            ['name' => 'Lucas Ferreira', 'email' => 'lucas.ferreira@meridian.com.br', 'department' => 'Operações', 'position' => 'Analista de Operações'],
            ['name' => 'Mariana Oliveira', 'email' => 'mariana.oliveira@meridian.com.br', 'department' => 'Consultoria', 'position' => 'Consultora de Negócios'],
        ];

        foreach ($travelers as $traveler) {
            User::create([
                'company_id' => $company->id,
                'name' => $traveler['name'],
                'email' => $traveler['email'],
                'password' => Hash::make('password'),
                'role' => 'traveler',
                'department' => $traveler['department'],
                'position' => $traveler['position'],
            ]);
        }

        $approvers = [
            ['name' => 'Fernando Nunes', 'email' => 'fernando.nunes@meridian.com.br', 'department' => 'Financeiro', 'position' => 'CFO'],
            ['name' => 'Claudia Ramos', 'email' => 'claudia.ramos@meridian.com.br', 'department' => 'RH', 'position' => 'Gerente de Viagens Corporativas'],
        ];

        foreach ($approvers as $approver) {
            User::create([
                'company_id' => $company->id,
                'name' => $approver['name'],
                'email' => $approver['email'],
                'password' => Hash::make('password'),
                'role' => 'approver',
                'department' => $approver['department'],
                'position' => $approver['position'],
            ]);
        }
    }
}
