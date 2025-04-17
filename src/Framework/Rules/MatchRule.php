<?php

declare(strict_types=1);

namespace Framework\Rules;

use Framework\Contracts\RuleInterface;

class MatchRule implements RuleInterface
{
    public function validate(array $formData, string $field, array $params): bool
    {
        $field_1 = $formData[$params[0]];
        $field_2 = $formData[$field];

        return $field_1 === $field_2;
    }

    public function getMessage(array $formData, string $field, array $params): string
    {
        return "Does not match field {$params[0]}";
    }
}
