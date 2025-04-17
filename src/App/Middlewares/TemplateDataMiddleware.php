<?php

declare(strict_types=1);

namespace App\Middlewares;

use Framework\Contracts\MiddlewareInterface;
use Framework\TemplateEngine;

class TemplateDataMiddleware implements MiddlewareInterface
{

    public function __construct(private TemplateEngine $view) {}

    public function process(callable $next)
    {
        $this->view->addGlobalTemplateData('title', 'Expense Tracking App.');
        $next();
    }
}
