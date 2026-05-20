<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelada;
use Illuminate\View\View;

class PeladaController extends Controller
{
    public function index(): View
    {
        return view('admin.peladas.index', [
            'peladas' => Pelada::with(['esporte', 'organizador'])->latest()->paginate(20),
        ]);
    }
}
