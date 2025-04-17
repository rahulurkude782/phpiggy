<?php

declare(strict_types=1);

namespace App\Middlewares;

use Framework\Contracts\MiddlewareInterface;
use Framework\TemplateEngine;

class FlashMiddleware implements MiddlewareInterface
{

    public function __construct(private TemplateEngine $view) {}

    public function process(callable $next)
    {
        $this->view->addGlobalTemplateData('errors', $_SESSION['errors'] ?? []);

        /* Unsetting the session varible from leaving any traces. */

        unset($_SESSION['errors']);

        $this->view->addGlobalTemplateData('oldFormData', $_SESSION['oldFormData'] ?? []);

        unset($_SESSION['oldFormData']);
        $next();
    }
}
