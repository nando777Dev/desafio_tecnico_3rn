<?php

namespace Database\Seeders;

use App\Models\Proposta;
use Illuminate\Database\Seeder;

class PropostaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Proposta::factory()->count(10)->create();
    }
}
