<?php

declare(strict_types=1);

function basePath(string $path = ''): string
{
    $root = defined('APP_PATH') ? APP_PATH : realpath(__DIR__ . '/../../');
    return rtrim($root, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
}
