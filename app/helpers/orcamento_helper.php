<?php

function calcularOrcamento(float $valorEstimado): array
{
    // Regra base: 30% de lucro
    $lucro = $valorEstimado * 0.30;

    // Margem em percentual
    $margem = ($lucro / $valorEstimado) * 100;

    return [
        'valor_estimado'  => round($valorEstimado, 2),
        'lucro_estimado'  => round($lucro, 2),
        'margem_estimada' => round($margem, 2),
    ];
}
