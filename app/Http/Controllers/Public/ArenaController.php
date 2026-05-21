<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Pelada;
use Illuminate\View\View;

class ArenaController extends Controller
{
    public function index(): View
    {
        $arenas = Pelada::query()
            ->select('cidade', 'bairro', 'local_nome', 'endereco')
            ->whereNotNull('local_nome')
            ->groupBy('cidade', 'bairro', 'local_nome', 'endereco')
            ->orderBy('cidade')
            ->get();

        return view('public.arenas', compact('arenas'));
    }
}
