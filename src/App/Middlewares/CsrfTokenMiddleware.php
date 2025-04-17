<?php

declare(strict_types=1);

namespace App\Middlewares;

use Framework\Contracts\MiddlewareInterface;
use Framework\TemplateEngine;

class CsrfTokenMiddleware implements MiddlewareInterface
{

    public function __construct(private TemplateEngine $view) {}

    public function process(callable $next)
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION['_token'] = $_SESSION['_token'] ?? $token;

        $this->view->addGlobalTemplateData('_token', $_SESSION['_token']);
        $next();
    }
}
