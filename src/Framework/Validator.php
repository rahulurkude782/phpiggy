<?php

declare(strict_types=1);

namespace Framework;

use Framework\Exceptions\ValidationException;
use function App\dd;
use Framework\Contracts\RuleInterface;

class Validator
{
    private array $errors = [];
    private array $rules = [];
    public function validate(array $formData, array $fields)
    {
        foreach ($fields as $fieldName => $rules) {
            foreach ($rules  as $rule) {

                $ruleParams = [];
                if (str_contains($rule, ':')) {
                    [$rule, $ruleParams] = explode(':', $rule);

                    $ruleParams = explode(',', $ruleParams);
                }

                $ruleValidatorInstance = $this->rules[$rule];

                if ($ruleValidatorInstance->validate($formData, $fieldName, $ruleParams)) {
                    continue;
                }

                $this->errors[$fieldName][] = $ruleValidatorInstance->getMessage($formData, $fieldName, $ruleParams);
            }
        }

        if (count($this->errors)) {
            throw new ValidationException($this->errors);
        }
    }

    public function add(string $alias, RuleInterface $rule)
    {
        $this->rules[$alias] = $rule;
    }
}
