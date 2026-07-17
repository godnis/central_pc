<?php

namespace App\Console\Commands;

use App\Enums\RoleUsuario;
use App\Enums\StatusMaquina;
use App\Mail\RelatorioParqueMail;
use App\Models\Maquina;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class VerificarParqueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:verificar-parque';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica máquinas antigas ou em manutenção há muito tempo e notifica os administradores';

    public function handle(): int
    {
        $maquinasAntigas = Maquina::with('setor')
            ->where('status', StatusMaquina::Ativa)
            ->whereNotNull('data_aquisicao')
            ->where('data_aquisicao', '<=', now()->subYears(3))
            ->orderBy('data_aquisicao')
            ->get();

        $maquinasEmManutencao = Maquina::with('setor')
            ->where('status', StatusMaquina::Manutencao)
            ->where('updated_at', '<=', now()->subDays(30))
            ->orderBy('updated_at')
            ->get();

        if ($maquinasAntigas->isEmpty() && $maquinasEmManutencao->isEmpty()) {
            $this->info('Nada a notificar.');

            return self::SUCCESS;
        }

        $admins = User::where('role', RoleUsuario::Admin)->pluck('email');

        if ($admins->isNotEmpty()) {
            Mail::to($admins->all())->send(new RelatorioParqueMail($maquinasAntigas, $maquinasEmManutencao));
        }

        $this->notificarWebhook($maquinasAntigas, $maquinasEmManutencao);

        $this->info("Notificação enviada: {$maquinasAntigas->count()} máquina(s) antiga(s), {$maquinasEmManutencao->count()} em manutenção prolongada.");

        return self::SUCCESS;
    }

    private function notificarWebhook(Collection $maquinasAntigas, Collection $maquinasEmManutencao): void
    {
        $url = config('services.notificacoes.webhook_url');

        if (! $url) {
            return;
        }

        Http::post($url, [
            'text' => "Central PC: {$maquinasAntigas->count()} máquina(s) com mais de 3 anos, "
                ."{$maquinasEmManutencao->count()} em manutenção há mais de 30 dias. Confira no sistema.",
        ]);
    }
}
