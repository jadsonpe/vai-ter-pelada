<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('peladas', function (Blueprint $table) {
            if (! Schema::hasColumn('peladas', 'cidade')) {
                return;
            }

            if (! $this->indexExists('peladas', 'peladas_cidade_index')) {
                $table->index('cidade');
            }

            if (! $this->indexExists('peladas', 'peladas_bairro_index')) {
                $table->index('bairro');
            }

            if (! $this->indexExists('peladas', 'peladas_esporte_id_index')) {
                $table->index('esporte_id');
            }

            if (! $this->indexExists('peladas', 'peladas_status_ativa_index')) {
                $table->index(['status', 'ativa'], 'peladas_status_ativa_index');
            }

            if (! $this->indexExists('peladas', 'peladas_valor_mensalista_index')) {
                $table->index('valor_mensalista');
            }

            if (! $this->indexExists('peladas', 'peladas_valor_diarista_index')) {
                $table->index('valor_diarista');
            }

            if (in_array(DB::getDriverName(), ['mysql', 'pgsql', 'mariadb'], true)) {
                try {
                    if (! $this->indexExists('peladas', 'peladas_fulltext_search_index')) {
                        $table->fullText(['nome', 'descricao', 'local_nome', 'endereco'], 'peladas_fulltext_search_index');
                    }
                } catch (\Exception $exception) {
                    // Fallback: DB does not support fulltext indexes in this connection.
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('peladas', function (Blueprint $table) {
            if ($this->indexExists('peladas', 'peladas_cidade_index')) {
                $table->dropIndex('peladas_cidade_index');
            }

            if ($this->indexExists('peladas', 'peladas_bairro_index')) {
                $table->dropIndex('peladas_bairro_index');
            }

            if ($this->indexExists('peladas', 'peladas_esporte_id_index')) {
                $table->dropIndex('peladas_esporte_id_index');
            }

            if ($this->indexExists('peladas', 'peladas_status_ativa_index')) {
                $table->dropIndex('peladas_status_ativa_index');
            }

            if ($this->indexExists('peladas', 'peladas_valor_mensalista_index')) {
                $table->dropIndex('peladas_valor_mensalista_index');
            }

            if ($this->indexExists('peladas', 'peladas_valor_diarista_index')) {
                $table->dropIndex('peladas_valor_diarista_index');
            }

            if ($this->indexExists('peladas', 'peladas_fulltext_search_index')) {
                $table->dropIndex('peladas_fulltext_search_index');
            }
        });
    }

    private function indexExists(string $table, string $index): bool
    {
        $connection = Schema::getConnection();

        if ($connection->getDriverName() === 'mysql' || $connection->getDriverName() === 'mariadb') {
            $database = $connection->getDatabaseName();
            $result = DB::selectOne("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$index]);
            return $result !== null;
        }

        if ($connection->getDriverName() === 'pgsql') {
            $result = DB::selectOne("SELECT 1 FROM pg_indexes WHERE tablename = ? AND indexname = ?", [$table, $index]);
            return $result !== null;
        }

        return false;
    }
};
