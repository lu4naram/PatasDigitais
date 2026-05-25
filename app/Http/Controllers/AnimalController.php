<?php

namespace App\Http\Controllers;

use App\Models\Especie;
use App\Models\Animal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Vacina;
use Illuminate\Support\Facades\Storage;
use App\Models\AnimalFoto;  
use Exception;

class AnimalController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $animais = Animal::with('fotos')
            ->where('adotado', false)  // FILTRO: SÓ NÃO ADOTADOS
            ->orderBy('created_at', 'desc')
            ->get();

        $especies = Especie::all();
        $vacinas = Vacina::all();

        return view('animal.index', compact('animais', 'especies', 'vacinas'));
    }
    

    public function create()
    {
        $especies = Especie::all();
        $vacinas = Vacina::all();

        // Verifique se tem dados
        if ($especies->isEmpty()) {
            \Log::warning('Nenhuma espécie encontrada!');
        }

        return view('animal.create', [
            'especies' => $especies,
            'vacinas' => $vacinas
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'nome' => 'required|max:100',
            'sexo' => 'required',
            'sobre' => 'required',
            'especie_id' => 'required|exists:especie,id',
            'fotos' => 'required|array|min:1',
            'fotos.*' => 'image|mimes:jpg,jpeg,png|max:5120'
        ]);

        try {
            DB::beginTransaction();

            $animal = Animal::create([
                'nome' => $request->nome,
                'sexo' => $request->sexo,
                'idade' => $request->idade ?? 0,
                'sobre' => $request->sobre,
                'castracao' => $request->castracao ?? 0,
                'especie_id' => $request->especie_id,
                'user_id' => auth()->id(),
                'adotado' => 0
            ]);

            $vacinas = json_decode($request->vacinas, true);
            if (!empty($vacinas) && is_array($vacinas)) {
                foreach ($vacinas as $nomeVacina) {
                    $vacina = Vacina::firstOrCreate(['nome' => $nomeVacina]);
                    $animal->vacinas()->syncWithoutDetaching([$vacina->id]);
                }
            }

            if ($request->hasFile('fotos')) {
                foreach ($request->file('fotos') as $foto) {
                    // Usando o disco 'r2' (configurado no .env)
                    $caminho = $foto->store('animais', 'r2');
                    $animal->fotos()->create(['caminho' => $caminho]);
                }
            }

            DB::commit();

            return redirect()->route('animais.index')
                ->with('success', 'Animal cadastrado com sucesso!');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao cadastrar animal: ' . $e->getMessage());

            return back()->with('error', 'Erro ao cadastrar animal. Tente novamente.')
                ->withInput();
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $animal = Animal::with(['especie', 'fotos', 'vacinas'])->findOrFail($id);
        return view("animal.show", compact('animal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $animal = Animal::findOrFail($id);
        $especies = Especie::all();
        return view('animal.edit', compact('especies', 'animal'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        \Log::info('UPDATE - Dados recebidos:', $request->all());
        $request->validate([
            'nome' => 'required|max:100',
            'sexo' => 'required',
            'sobre' => 'required',
            'especie_id' => 'required|exists:especie,id',
        ]);

        try {
            DB::beginTransaction();

            $animal = Animal::findOrFail($id);

            // Atualizar dados do animal
            $animal->update([
                'nome' => $request->nome,
                'sexo' => $request->sexo,
                'idade' => $request->idade ?? 0,
                'sobre' => $request->sobre,
                'castracao' => $request->castracao ?? 0,
                'especie_id' => $request->especie_id,
            ]);

            // Remover fotos marcadas para exclusão
            if ($request->has('fotos_remover')) {
                foreach ($request->fotos_remover as $fotoId) {
                    $foto = $animal->fotos()->find($fotoId);
                    if ($foto) {
                        // Remover arquivo do storage
                        Storage::disk('r2')->delete($foto->caminho);
                        $foto->delete();
                    }
                }
            }

            // Adicionar novas fotos no R2
            if ($request->hasFile('fotos')) {
                foreach ($request->file('fotos') as $foto) {
                    $caminho = $foto->store('animais', 'r2');
                    $animal->fotos()->create(['caminho' => $caminho]);
                }
            }

            // Atualizar vacinas
            $vacinas = json_decode($request->vacinas, true);
            if (!empty($vacinas) && is_array($vacinas)) {
                $vacinasIds = [];
                foreach ($vacinas as $nomeVacina) {
                    $vacina = Vacina::firstOrCreate(['nome' => $nomeVacina]);
                    $vacinasIds[] = $vacina->id;
                }
                $animal->vacinas()->sync($vacinasIds);
            } else {
                $animal->vacinas()->detach();
            }

            DB::commit();

            return redirect()->route('animais.index')
                ->with('success', 'Animal atualizado com sucesso!');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar animal: ' . $e->getMessage());

            return back()->with('error', 'Erro ao atualizar animal. Tente novamente.')
                ->withInput();
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $animal = Animal::findOrFail($id);

            // Verificar permissão
            if (auth()->user()->isAdmin() || auth()->id() === $animal->user_id) {
                // Remover fotos do R2
                foreach ($animal->fotos as $foto) {
                    Storage::disk('r2')->delete($foto->caminho);
                    $foto->delete();
                }
                $animal->delete();
                return redirect()->route('animais.index')->with('success', 'Animal excluído com sucesso!');
            }


            return redirect()->route('animais.index')->with('error', 'Sem permissão para excluir.');

        } catch (Exception $e) {
            Log::error('Erro ao excluir animal: ' . $e->getMessage());
            return back()->with('error', 'Erro ao excluir animal.');
        }
    }
    public function meusAnuncios()
    {
        $animais = Animal::with(['fotos', 'usuario'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
    
        // Buscar espécies e vacinas para o modal
        $especies = Especie::all();
        $vacinas = Vacina::all();
    
    return view('postados.meus-anuncios', compact('animais', 'especies', 'vacinas'));
    }
    public function removerFoto($fotoId)
    {
        try {
            $foto = AnimalFoto::findOrFail($fotoId);
            $animal = $foto->animal;

            // Verificar permissão
            if (auth()->user()->isAdmin() || auth()->id() === $animal->user_id) {
                Storage::disk('r2')->delete($foto->caminho);
                $foto->delete();
                return response()->json(['success' => true]);
            }

            return response()->json(['error' => 'Sem permissão'], 403);

        } catch (Exception $e) {
            Log::error('Erro ao remover foto: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao remover foto'], 500);
        }
    }
}
