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
