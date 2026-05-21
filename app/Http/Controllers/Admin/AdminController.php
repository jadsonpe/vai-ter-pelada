<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Esporte;
use App\Models\Patrocinador;
use App\Models\Pelada;
use App\Models\User;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'usuariosCount' => User::count(),
            'organizadoresCount' => User::where('role', 'organizador')->count(),
            'peladasCount' => Pelada::count(),
            'esportesCount' => Esporte::count(),
            'bannersCount' => Banner::count(),
            'patrocinadoresCount' => Patrocinador::count(),
        ]);
    }
}
