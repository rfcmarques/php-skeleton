<?php

declare(strict_types=1);

function redirect(string $url, int $status = 302): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && $status === 302) {
        $status = 303;
    }

    header("Location: {$url}", true, $status);
    exit();
}
