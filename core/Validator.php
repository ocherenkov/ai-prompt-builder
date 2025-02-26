<?php

namespace Core;

use Core\Exceptions\ValidationException;

final class Validator
{
    /**
     * @throws ValidationException
     */
    public static function validate($data, $rules): array
    {
        $errors = [];
        $validatedData = [];

        foreach ($rules as $field => $ruleString) {
            $rulesList = explode('|', $ruleString);

            $isSometimes = in_array('sometimes', $rulesList, true);

            if ($isSometimes && !isset($data[$field])) {
                continue;
            }

            foreach ($rulesList as $rule) {
                if ($rule === 'sometimes') {
                    continue;
                }

                [$ruleName, $parameter] = array_pad(explode(':', $rule, 2), 2, null);

                $value = $data[$field] ?? null;
                if (!self::applyRule($value, $ruleName, $parameter, $data)) {
                    $errors[$field][] = self::errorMessage($field, $ruleName, $parameter);
                }

                if (isset($data[$field])) {
                    $validatedData[$field] = $data[$field];
                }
            }
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        return $validatedData;
    }

    private static function applyRule(mixed $value, string $rule, ?string $parameter, array $data): bool
    {
        return match ($rule) {
            'required' => !empty($value),
            'min' => strlen((string)$value) >= (int)$parameter,
            'max' => strlen((string)$value) <= (int)$parameter,
            'email' => filter_var($value, FILTER_VALIDATE_EMAIL) !== false,
            'numeric' => is_numeric($value),
            'url' => filter_var($value, FILTER_VALIDATE_URL) !== false,
            'regex' => preg_match($parameter, $value) === 1,
            'same' => isset($data[$parameter]) && $value === $data[$parameter],
            'boolean' => in_array($value, ['true', 'false', '0', '1'], true),
            'nullable' => $value === null,
            default => true
        };
    }

    private static function errorMessage(string $field, string $rule, ?string $parameter): string
    {
        return match ($rule) {
            'required' => "The $field field is required.",
            'min' => "The $field field must be at least $parameter characters.",
            'max' => "The $field field must not exceed $parameter characters.",
            'email' => "The $field field must be a valid email address.",
            'numeric' => "The $field field must be a number.",
            'alpha' => "The $field field must contain only letters.",
            'alpha_num' => "The $field field must contain only letters and numbers.",
            'url' => "The $field field must be a valid URL.",
            'regex' => "The $field field format is invalid.",
            'same' => "The $field field must match the $parameter field.",
            default => "Invalid value for $field."
        };
    }
}
