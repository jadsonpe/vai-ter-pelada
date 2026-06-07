<?php

namespace App\Http\Controllers;

use App\Models\Notificacao;
use App\Models\Pelada;
use App\Models\PlayerPost;
use App\Models\PlayerProfile;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    public function storePelada(Request $request, Pelada $pelada): RedirectResponse
    {
        $data = $this->validateReport($request, 'pelada');

        if ($pelada->organizador_id === $request->user()->id) {
            return back()->with('status', 'Você não pode denunciar uma pelada que você organiza.');
        }

        $this->store($request, $pelada, 'pelada', $data, $pelada->nome, route('peladas.show', $pelada));

        return back()->with('status', 'Denúncia enviada. Nossa equipe vai analisar as informações.');
    }

    public function storePlayer(Request $request, PlayerProfile $profile): RedirectResponse
    {
        abort_unless($profile->publico, 404);

        $data = $this->validateReport($request, 'jogador');

        if ($profile->user_id === $request->user()->id) {
            return back()->with('status', 'Você não pode denunciar o próprio perfil.');
        }

        $name = $profile->user?->apelido ?: $profile->user?->name ?: 'Peladeiro';
        $this->store($request, $profile, 'jogador', $data, $name, route('peladeiros.show', $profile));

        return back()->with('status', 'Denúncia enviada. Nossa equipe vai analisar as informações.');
    }

    public function storePlayerPost(Request $request, PlayerPost $post): RedirectResponse
    {
        abort_unless($post->status === PlayerPost::STATUS_PUBLICADO, 404);

        $data = $this->validateReport($request, 'publicacao');

        if ($post->user_id === $request->user()->id) {
            return back()->with('status', 'Você não pode denunciar a própria publicação.');
        }

        $name = 'Publicação de '.($post->user?->apelido ?: $post->user?->name ?: 'Peladeiro');
        $url = ($post->profile?->shareUrl() ?: route('peladeiros.show', $post->profile)).'#publicacao-'.$post->id;

        $this->store($request, $post, 'publicação', $data, $name, $url);

        return back()->with('status', 'Denúncia enviada. Nossa equipe vai analisar as informações.');
    }

    private function validateReport(Request $request, string $type): array
    {
        return $request->validate([
            'reason' => ['required', Rule::in(array_keys(Report::reasonsFor($type)))],
            'description' => ['nullable', 'string', 'min:10', 'max:2000'],
        ], [
            'reason.required' => 'Selecione um motivo para a denúncia.',
            'reason.in' => 'Selecione um motivo válido para a denúncia.',
            'description.min' => 'Explique a denúncia com pelo menos 10 caracteres.',
            'description.max' => 'A descrição da denúncia deve ter no máximo 2000 caracteres.',
        ]);
    }

    private function store(Request $request, $reportable, string $type, array $data, string $targetName, string $targetUrl): void
    {
        $existing = Report::query()
            ->where('reporter_id', $request->user()->id)
            ->where('reportable_type', $reportable::class)
            ->where('reportable_id', $reportable->id)
            ->whereIn('status', [Report::STATUS_PENDING, Report::STATUS_REVIEWING])
            ->first();

        if ($existing) {
            return;
        }

        $report = Report::create([
            'reporter_id' => $request->user()->id,
            'reportable_type' => $reportable::class,
            'reportable_id' => $reportable->id,
            'reason' => $data['reason'],
            'description' => $data['description'] ?? null,
            'status' => Report::STATUS_PENDING,
            'metadata' => [
                'type' => $type,
                'target_name' => $targetName,
                'target_url' => $targetUrl,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
        ]);

        User::query()
            ->where('role', 'admin')
            ->get()
            ->each(function (User $admin) use ($report, $type, $targetName, $targetUrl) {
                Notificacao::create([
                    'user_id' => $admin->id,
                    'titulo' => 'Nova denúncia recebida',
                    'mensagem' => sprintf('Denúncia #%s sobre %s: %s.', $report->id, $type, $targetName),
                    'link' => $targetUrl,
                ]);
            });
    }
}
