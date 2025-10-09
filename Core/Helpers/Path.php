<?php

function basePath(string $path = ''): string
{
    return APP_PATH . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
}