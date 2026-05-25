<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VacinasSeeder extends Seeder
{
    public function run(): void
    {
        $canina = DB::table('especie')->where('nome', 'Canina')->first();
        $felina = DB::table('especie')->where('nome', 'Felina')->first();

        DB::table('vacinas')->insert([

            [
                'nome' => 'V8',
                'descricao' => 'Protege contra cinomose, parvovirose, hepatite, leptospirose e outras',
                'especie_id' => $canina->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nome' => 'V10',
                'descricao' => 'Similar à V8 com mais sorotipos de leptospirose',
                'especie_id' => $canina->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nome' => 'Antirrábica',
                'descricao' => 'Proteção contra raiva',
                'especie_id' => $canina->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            [
                'nome' => 'V3',
                'descricao' => 'Protege contra panleucopenia, calicivirose e rinotraqueíte',
                'especie_id' => $felina->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nome' => 'V4',
                'descricao' => 'V3 + clamidiose',
                'especie_id' => $felina->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nome' => 'V5',
                'descricao' => 'V4 + leucemia felina (FeLV)',
                'especie_id' => $felina->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nome' => 'Antirrábica',
                'descricao' => 'Proteção contra raiva',
                'especie_id' => $felina->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}