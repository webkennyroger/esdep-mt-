<?php

use App\Support\TailwindColors;

test('palette has 22 colors, each a valid hex mapped to a portuguese label', function () {
    $palette = TailwindColors::palette();

    expect($palette)->toHaveCount(22);

    foreach ($palette as $hex => $label) {
        expect($hex)->toMatch('/^#[0-9a-fA-F]{6}$/')
            ->and($label)->toBeString()->not->toBeEmpty();
    }
});

test('darken reduces each rgb channel by the given percentage', function () {
    expect(TailwindColors::darken('#008b5c', 0.3))->toBe('#006140')
        ->and(TailwindColors::darken('#ffffff', 0.5))->toBe('#808080')
        ->and(TailwindColors::darken('#000000', 0.5))->toBe('#000000');
});
