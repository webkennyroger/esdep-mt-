<?php

test('login page has a dark mode toggle', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('id="theme-toggle"', false);
});
