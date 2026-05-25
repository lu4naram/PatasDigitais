<?php

namespace App\Http\Controllers;

use App\Models\Especie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class EspecieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $especies = Especie::all();
        return view('especie.index', compact('especies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("especie.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $request->validate([
        'nome' => 'required|string|max:255'
    ]);

    $especie = Especie::create([
        'nome' => $request->nome
    ]);

    return response()->json([
        'success' => true,
        'data' => $especie
    ]);
}

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $especie = Especie::findOrFail($id);
        return view("especie.show", compact('especie'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $especie = Especie::findOrFail($id);
        return view('especie.edit', compact('especie'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try{
            $especie = Especie::findOrFail($id);
            $especie->update($request->all());
        } catch(Exception $e){
            Log::error('Erro ao alterar especie: '. $e->getMessage(), [
                'stack' => $e->getTraceAsString()
            ]);
        }
        return redirect()->route('especies.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try{
            $especie = Especie::findOrFail($id);
            $especie->delete();
        } catch(Exception $e){
            Log::error('Erro ao excluir especie: '. $e->getMessage(), [
                'stack' => $e->getTraceAsString()
            ]);
        }
        return redirect()->route('especies.index');
    }
}
