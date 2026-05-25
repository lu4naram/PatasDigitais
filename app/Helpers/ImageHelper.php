<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class ImageHelper
{
    public static function url($caminho)
    {
        if (!$caminho) {
            return asset('images/sem-imagem.jpg');
        }
        
        try {
            // Para R2, gera URL temporária (válida por 1 hora)
            if (config('filesystems.default') === 'r2') {
                return Storage::disk('r2')->temporaryUrl($caminho, now()->addMinutes(60));
            }
            
            // Fallback para URL direta (se não for R2)
            return asset('storage/' . $caminho);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao gerar URL da imagem: ' . $e->getMessage());
            return asset('images/sem-imagem.jpg');
        }
    }
    
    // Método para URL pública (se o bucket for público)
    public static function publicUrl($caminho)
    {
        if (!$caminho) {
            return asset('images/sem-imagem.jpg');
        }
        
        // URL pública do R2 (se o bucket for público)
        $r2Url = config('filesystems.disks.r2.url');
        if ($r2Url) {
            return rtrim($r2Url, '/') . '/' . ltrim($caminho, '/');
        }
        
        return self::url($caminho);
    }
}
