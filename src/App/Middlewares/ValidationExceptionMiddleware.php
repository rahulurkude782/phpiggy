<?php

declare(strict_types=1);

namespace App\Middlewares;

use Framework\Contracts\MiddlewareInterface;
use Framework\Exceptions\ValidationException;

class ValidationExceptionMiddleware implements MiddlewareInterface
{
    public function process(callable $next)
    {
        try {
            $next();
        } catch (ValidationException $e) {
            $_SESSION['errors'] = $e->errors;

            $oldFormData = $_POST;

            $excludedFields = ['password', 'confirm_password'];

            $oldFormData = array_diff_key($oldFormData, array_flip($excludedFields));

            $_SESSION['oldFormData'] = $oldFormData;
            redirectTo($_SERVER['HTTP_REFERER']);
        }
    }
}
