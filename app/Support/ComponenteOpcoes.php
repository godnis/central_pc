<?php

namespace App\Support;

/**
 * Listas de valores sugeridos para os campos de specs no formulário do
 * catálogo. São sugestões (datalist), não uma enumeração fechada no banco —
 * o texto digitado pelo usuário é o que efetivamente é salvo.
 */
class ComponenteOpcoes
{
    public static function formFactors(): array
    {
        return ['ATX', 'Micro-ATX', 'Mini-ITX', 'E-ATX'];
    }

    public static function tiposRam(): array
    {
        return ['DDR3', 'DDR4', 'DDR5'];
    }

    public static function interfacesArmazenamento(): array
    {
        return ['SATA', 'NVMe', 'M.2 SATA'];
    }

    public static function tiposArmazenamento(): array
    {
        return ['HD', 'SSD'];
    }
}
