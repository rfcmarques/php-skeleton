<?php

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
