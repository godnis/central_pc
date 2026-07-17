<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class BackupDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera um dump do banco (pg_dump) em storage/backups e apaga backups com mais de 14 dias';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $config = config('database.connections.'.config('database.default'));

        if (($config['driver'] ?? null) !== 'pgsql') {
            $this->error('Este comando só sabe fazer backup de banco PostgreSQL (driver atual: '.($config['driver'] ?? 'desconhecido').').');

            return self::FAILURE;
        }

        $diretorio = storage_path('backups');
        if (! is_dir($diretorio)) {
            mkdir($diretorio, 0755, true);
        }

        $arquivo = $diretorio.DIRECTORY_SEPARATOR.'central_pc_'.now()->format('Y-m-d_His').'.dump';

        $processo = new Process([
            'pg_dump',
            '-h', $config['host'],
            '-p', (string) $config['port'],
            '-U', $config['username'],
            '-F', 'c',
            '-f', $arquivo,
            $config['database'],
        ]);
        $processo->setEnv(['PGPASSWORD' => $config['password']]);
        $processo->setTimeout(300);
        $processo->run();

        if (! $processo->isSuccessful()) {
            $this->error('Falha ao gerar backup: '.$processo->getErrorOutput());

            return self::FAILURE;
        }

        $this->info("Backup gerado em {$arquivo}");

        $this->limparBackupsAntigos($diretorio);

        return self::SUCCESS;
    }

    private function limparBackupsAntigos(string $diretorio): void
    {
        $limite = now()->subDays(14)->getTimestamp();

        foreach (glob($diretorio.DIRECTORY_SEPARATOR.'central_pc_*.dump') as $arquivo) {
            if (filemtime($arquivo) < $limite) {
                unlink($arquivo);
            }
        }
    }
}
