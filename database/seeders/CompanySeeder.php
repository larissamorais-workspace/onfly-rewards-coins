<?php
namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('companies')->truncate();

        Company::create([
            'name' => 'Meridian Construções e Engenharia Ltda',
            'cnpj' => '47.832.165/0001-93',
        ]);
    }
}
