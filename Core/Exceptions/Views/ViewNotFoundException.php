<?php

namespace Core\Exceptions\Views;

use Exception;

class ViewNotFoundException extends Exception
{
    public function __construct(string $viewName)
    {
        parent::__construct("View not found: {$viewName}");
    }
}