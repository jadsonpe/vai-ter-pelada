<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Patrocinador;
use Illuminate\View\View;

class PatrocinadorController extends Controller
{
    public function index(): View
    {
        return view('public.patrocinadores', [
            'patrocinadores' => Patrocinador::where('ativo', true)->latest()->get(),
        ]);
    }
}
