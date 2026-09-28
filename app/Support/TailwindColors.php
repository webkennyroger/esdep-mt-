<?php

namespace App\Support;

/**
 * Paleta unica de cores usada em todo seletor de cor do sistema (sala, grupo de
 * contato, etc), pra nao duplicar a lista em cada componente. Um tom por familia
 * de cor do Tailwind (nivel 600, o mesmo tom que ja era usado nas cores antigas).
 */
class TailwindColors
{
    /**
     * Escurece uma cor hex por uma porcentagem (0 a 1), pra montar um gradiente
     * de duas pontas a partir da unica cor cadastrada na sala.
     */
    public static function darken(string $hex, float $percent): string
    {
        $hex = ltrim($hex, '#');
        $r = max(0, (int) round(hexdec(substr($hex, 0, 2)) * (1 - $percent)));
        $g = max(0, (int) round(hexdec(substr($hex, 2, 2)) * (1 - $percent)));
        $b = max(0, (int) round(hexdec(substr($hex, 4, 2)) * (1 - $percent)));

        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    public static function palette(): array
    {
        return [
            '#475569' => 'Ardósia',
            '#4b5563' => 'Cinza',
            '#52525b' => 'Zinco',
            '#525252' => 'Neutro',
            '#57534e' => 'Pedra',
            '#dc2626' => 'Vermelho',
            '#ea580c' => 'Laranja',
            '#d97706' => 'Âmbar',
            '#ca8a04' => 'Amarelo',
            '#65a30d' => 'Lima',
            '#16a34a' => 'Verde',
            '#059669' => 'Esmeralda',
            '#0d9488' => 'Verde-azulado',
            '#0891b2' => 'Ciano',
            '#0284c7' => 'Azul-celeste',
            '#2563eb' => 'Azul',
            '#4f46e5' => 'Índigo',
            '#7c3aed' => 'Violeta',
            '#9333ea' => 'Púrpura',
            '#c026d3' => 'Magenta',
            '#db2777' => 'Rosa',
            '#e11d48' => 'Rosé',
        ];
    }
}
