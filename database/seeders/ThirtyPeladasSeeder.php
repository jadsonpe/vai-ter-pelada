<?php

namespace Database\Seeders;

use App\Models\Esporte;
use App\Models\Pelada;
use App\Models\PeladaMembro;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ThirtyPeladasSeeder extends Seeder
{
    public function run(): void
    {
        $esportes = Esporte::where('ativo', true)->orderBy('id')->get();

        if ($esportes->isEmpty()) {
            foreach (['Futebol', 'Futsal', 'Society', 'Volei', 'Basquete'] as $nome) {
                $esportes->push(Esporte::firstOrCreate(
                    ['slug' => Str::slug($nome)],
                    ['nome' => $nome, 'ativo' => true]
                ));
            }
        }

        $users = User::query()
            ->where('status', '!=', 'bloqueado')
            ->orderBy('id')
            ->limit(30)
            ->get();

        if ($users->count() < 30) {
            $users = User::query()->orderBy('id')->limit(30)->get();
        }

        $cidades = [
            ['cidade' => 'Recife', 'bairro' => 'Boa Viagem', 'uf' => 'PE'],
            ['cidade' => 'Sao Paulo', 'bairro' => 'Tatuape', 'uf' => 'SP'],
            ['cidade' => 'Rio de Janeiro', 'bairro' => 'Tijuca', 'uf' => 'RJ'],
            ['cidade' => 'Belo Horizonte', 'bairro' => 'Pampulha', 'uf' => 'MG'],
            ['cidade' => 'Salvador', 'bairro' => 'Pituba', 'uf' => 'BA'],
            ['cidade' => 'Fortaleza', 'bairro' => 'Meireles', 'uf' => 'CE'],
            ['cidade' => 'Curitiba', 'bairro' => 'Batel', 'uf' => 'PR'],
            ['cidade' => 'Porto Alegre', 'bairro' => 'Cidade Baixa', 'uf' => 'RS'],
            ['cidade' => 'Manaus', 'bairro' => 'Adrianopolis', 'uf' => 'AM'],
            ['cidade' => 'Goiania', 'bairro' => 'Setor Bueno', 'uf' => 'GO'],
        ];

        foreach ($users->values() as $index => $user) {
            $esporte = $esportes[$index % $esportes->count()];
            $local = $cidades[$index % count($cidades)];
            $nome = 'Pelada Teste '.$this->number($index + 1).' '.$local['uf'];
            $slug = Str::slug('pelada-extra-'.$index.'-'.$user->id);

            $pelada = Pelada::updateOrCreate(
                ['slug' => $slug],
                [
                    'organizador_id' => $user->id,
                    'esporte_id' => $esporte->id,
                    'nome' => $nome,
                    'descricao' => 'Pelada extra criada para teste com organizador diferente ja cadastrado no banco.',
                    'data_fundacao' => now()->subMonths(($index % 24) + 1)->toDateString(),
                    'categoria' => $index % 4 === 0 ? 'infantil' : 'adulto',
                    'cidade' => $local['cidade'],
                    'bairro' => $local['bairro'],
                    'local_nome' => 'Arena '.$local['uf'].' Extra '.($index + 1),
                    'endereco' => 'Rua do Teste, '.(100 + $index),
                    'local' => 'Arena '.$local['uf'].' Extra '.($index + 1),
                    'dia_semana' => $index % 7,
                    'horario' => sprintf('%02d:00', 18 + ($index % 4)),
                    'vagas_totais' => 12 + ($index % 14),
                    'vagas_diaristas' => 2 + ($index % 5),
                    'aceita_diarista' => true,
                    'requer_aprovacao' => $index % 2 === 0,
                    'capacidade' => 12 + ($index % 14),
                    'valor_mensalista' => 70 + ($index * 3),
                    'valor_diarista' => 15 + ($index % 8) * 5,
                    'status' => 'ativa',
                    'regras' => "1. Respeito.\n2. Pontualidade.\n3. Sorteio equilibrado.",
                    'whatsapp_contato' => $user->phone,
                    'ativa' => true,
                ]
            );

            PeladaMembro::updateOrCreate(
                ['pelada_id' => $pelada->id, 'user_id' => $user->id],
                [
                    'apelido' => $user->apelido,
                    'tipo' => 'mensalista',
                    'status' => 'ativo',
                    'prioridade' => 1,
                    'data_entrada' => now()->toDateString(),
                    'mensalista_desde' => now()->toDateString(),
                    'observacao' => 'Organizador da pelada extra.',
                ]
            );
        }
    }

    private function number(int $number): string
    {
        return str_pad((string) $number, 2, '0', STR_PAD_LEFT);
    }
}
