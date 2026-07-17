<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RelatorioParqueMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  Collection  $maquinasAntigas  máquinas com mais de N anos de aquisição
     * @param  Collection  $maquinasEmManutencao  máquinas em manutenção há mais de N dias
     */
    public function __construct(
        public Collection $maquinasAntigas,
        public Collection $maquinasEmManutencao,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Central PC — relatório de manutenção preventiva',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.relatorio-parque',
        );
    }
}
