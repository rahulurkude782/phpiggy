<?php

declare(strict_types=1);

namespace Framework\Rules;

use Framework\Contracts\RuleInterface;
use InvalidArgumentException;

class MaxRule implements RuleInterface
{
    public function validate(array $formData, string $field, array $params): bool
    {
        if (empty($params[0])) {
            throw new InvalidArgumentException('Maximum length is not specified.');
        }

        $length = (int) $params[0];

        return strlen($formData[$field]) <= $length;
    }

    public function getMessage(array $formData, string $field, array $params): string
    {
        return "Exceeded maximum character limit of {$params[0]}";
    }
}
