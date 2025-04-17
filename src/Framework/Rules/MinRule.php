<?php

declare(strict_types=1);

namespace Framework\Rules;

use Framework\Contracts\RuleInterface;

class MinRule implements RuleInterface
{
    public function validate(array $formData, string $field, array $params): bool
    {
        return $formData[$field] >= $params[0];
    }

    public function getMessage(array $formData, string $field, array $params): string
    {
        return "Must be at least {$params[0]}";
    }
}
