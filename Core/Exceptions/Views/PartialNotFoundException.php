<?php

namespace Core\Exceptions\Views;

use Exception;

class PartialNotFoundException extends Exception
{
    public function __construct(string $partialName)
    {
        parent::__construct("Partial not found: {$partialName}");
    }
}