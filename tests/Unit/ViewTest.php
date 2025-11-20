<?php

use Core\Exceptions\Views\PartialNotFoundException;
use Core\Exceptions\Views\ViewNotFoundException;

it('renders partials and view', function () {
    $out = function () {
        view('home'); // usa head/footer corrigidos
    };

    ob_start();
    $out();
    $html = ob_get_clean();

    expect($html)->toContain('<!DOCTYPE html>');
    expect($html)->toContain('Welcome to the Home Page');
});

it('throws ViewNotFoundException for a missing view', function () {
    view('this-view-does-not-exist');
})->throws(ViewNotFoundException::class);

it('throws PartialNotFoundException for a missing partial', function () {
    partial('this-partial-does-not-exist');
})->throws(PartialNotFoundException::class);
