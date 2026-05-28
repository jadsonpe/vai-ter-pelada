<?php

namespace Database\Seeders;

use App\Models\Pelada;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThirtyMembersPerPeladaSeeder extends Seeder
{
    public function run(): void
    {
        $needsMembers = Pelada::withCount('membros')
            ->get()
            ->contains(fn (Pelada $pelada) => $pelada->membros_count < 30);

        if (! $needsMembers) {
            return;
        }

        $users = User::query()
            ->where('status', '!=', 'bloqueado')
            ->orderBy('id')
            ->get();

        if ($users->count() < 30) {
            $users = User::query()->orderBy('id')->get();
        }

        if ($users->isEmpty()) {
            return;
        }

        Pelada::withCount('membros')
            ->orderBy('id')
            ->chunkById(50, function ($peladas) use ($users) {
                foreach ($peladas as $pelada) {
                    if ($pelada->membros_count >= 30) {
                        continue;
                    }

                    $selected = $users
                        ->sortBy(fn (User $user) => crc32($pelada->id.'-'.$user->id))
                        ->take(min(30, $users->count()))
                        ->values();

                    if (! $selected->contains('id', $pelada->organizador_id)) {
                        $organizer = User::find($pelada->organizador_id);
                        if ($organizer) {
                            $selected->pop();
                            $selected->prepend($organizer);
                        }
                    }

                    $now = now();
                    $rows = [];

                    foreach ($selected as $index => $user) {
                        $date = now()->subDays(30 - min($index, 29))->toDateString();
                        $rows[] = [
                                'pelada_id' => $pelada->id,
                                'user_id' => $user->id,
                                'apelido' => $user->apelido,
                                'tipo' => 'mensalista',
                                'status' => 'ativo',
                                'prioridade' => $index + 1,
                                'data_entrada' => $date,
                                'mensalista_desde' => $date,
                                'observacao' => 'Mensalista demo para teste de carga.',
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                    }

                    DB::table('pelada_membros')->upsert(
                        $rows,
                        ['pelada_id', 'user_id'],
                        ['apelido', 'tipo', 'status', 'prioridade', 'data_entrada', 'mensalista_desde', 'observacao', 'updated_at']
                    );
                }
            });
    }
}
