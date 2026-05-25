<?php

use App\Models\Vacina;
use Illuminate\Support\Facades\Route;

Route::get('/vacinas/{especieId}', function ($especieId) {
    $vacinas = Vacina::where('especie_id', $especieId)
                     ->select('id', 'nome')
                     ->get();
    
    return response()->json($vacinas);
});