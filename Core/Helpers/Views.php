<?php

function view(string $name, array $data = []): void
{
    $viewPath = basePath("App/Views/{$name}.view.php");

    if (!file_exists($viewPath)) {
        throw new \Core\Exceptions\Views\ViewNotFoundException($name);
    }

    extract($data);
    require $viewPath;
}

function partial (string $name, array $data = []): void
{
    $partialPath = basePath("App/Views/Partials/{$name}.partial.php");

    if (!file_exists($partialPath)) {
        throw new \Core\Exceptions\Views\PartialNotFoundException($name);
    }

    extract($data);
    require $partialPath;
}