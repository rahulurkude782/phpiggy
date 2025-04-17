<?php

declare(strict_types=1);

namespace Framework\Rules;

use Framework\Contracts\RuleInterface;

class DateFormatRule implements RuleInterface
{
    public function validate(array $formData, string $field, array $params): bool
    {
        $parsedFormat = date_parse_from_format($params[0], $formData[$field]);
        return $parsedFormat['error_count'] === 0 && $parsedFormat['warning_count'] === 0;
    }

    public function getMessage(array $formData, string $field, array $params): string
    {
        return "Invalid Date";
    }
}
