<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Exceptions\SessionException;
use Framework\Contracts\MiddlewareInterface;

class SessionMiddleware implements MiddlewareInterface
{
    public function process(callable $next)
    {

        /* Checks whether the sessions has already been started. */

        if (session_status() === PHP_SESSION_ACTIVE) {
            throw new SessionException("Session is already started.");
        }


        /* Checks whether headers has already been sent to browser. */
        if (headers_sent($fileName, $line)) {
            throw new SessionException("Headers already been sent. Consider removing content at {$fileName} line {$line}.");
        }

        session_set_cookie_params([
            'secure' => $_ENV['APP_ENV'] === 'production',
            'httponly' => true,
            'samesite' => 'lax'
        ]);

        session_start();

        $next();

        session_write_close();
    }
}
