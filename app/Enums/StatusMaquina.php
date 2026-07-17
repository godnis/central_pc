<?php

namespace App\Enums;

enum StatusMaquina: string
{
    case Ativa = 'ativa';
    case Manutencao = 'manutencao';
    case Baixada = 'baixada';

    public function label(): string
    {
        return match ($this) {
            self::Ativa => 'Ativa',
            self::Manutencao => 'Em manutenção',
            self::Baixada => 'Baixada',
        };
    }
}
