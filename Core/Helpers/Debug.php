<?php

declare(strict_types=1);

/**
 * Dumps information about 
 * the variable(s) passed
 * to it.
 * @param mixed[] $vars
 * @return void
 */
function dump(mixed ...$vars): void
{
    foreach ($vars as $var) {
        echo '<div style="background: #f9f9f9; border: 1px solid #ccc; padding: 10px; margin: 10px 0;">';
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
        echo '</div>';
    }
}

/**
 * Dumps information about
 * the variable(s) passed 
 * to it and terminates
 * the script.
 * @param mixed[] $vars
 * @return never
 */
function dd(mixed ...$vars): never
{
    dump(...$vars);
    die();
}
