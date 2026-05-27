<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $reports = Report::query()
            ->with(['reporter', 'reportable'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.reports.index', [
            'reports' => $reports,
            'statuses' => [
                Report::STATUS_PENDING => 'Pendente',
                Report::STATUS_REVIEWING => 'Em analise',
                Report::STATUS_RESOLVED => 'Resolvida',
                Report::STATUS_REJECTED => 'Improcedente',
            ],
        ]);
    }

    public function update(Request $request, Report $report): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', [
                Report::STATUS_PENDING,
                Report::STATUS_REVIEWING,
                Report::STATUS_RESOLVED,
                Report::STATUS_REJECTED,
            ])],
            'resolution' => ['nullable', 'string', 'max:2000'],
        ]);

        $report->update([
            'status' => $data['status'],
            'resolution' => $data['resolution'] ?? null,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('status', 'Denuncia atualizada.');
    }
}
