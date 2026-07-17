<?php

namespace App\Enums;

enum RoleUsuario: string
{
    case Admin = 'admin';
    case Tecnico = 'tecnico';
    case Leitura = 'leitura';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrador',
            self::Tecnico => 'Técnico',
            self::Leitura => 'Somente leitura',
        };
    }
}
