<?php

declare(strict_types=1);

namespace App\Controllers;

use App\View;

abstract class Controller
{
    protected function view(string $template, array $data = []): void
    {
        View::render($template, $data);
    }

    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    protected function redirect(string $url, int $status = 302): void
    {
        http_response_code($status);
        header("Location: {$url}");
        exit;
    }

    protected function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        $this->redirect($referer);
    }

    protected function validate(array $rules, array $data): array
    {
        $errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;

            foreach (explode('|', $fieldRules) as $rule) {
                $error = $this->validateRule($field, $value, $rule, $data);
                if ($error) {
                    $errors[$field] = $error;
                    break;
                }
            }
        }

        return $errors;
    }

    private function validateRule(string $field, mixed $value, string $rule, array $data): ?string
    {
        [$ruleName, $parameter] = array_pad(explode(':', $rule, 2), 2, null);

        return match ($ruleName) {
            'required' => empty($value) ? "The {$field} field is required." : null,
            'email' => !filter_var($value, FILTER_VALIDATE_EMAIL) ? "The {$field} must be a valid email." : null,
            'min' => strlen($value ?? '') < (int) $parameter ? "The {$field} must be at least {$parameter} characters." : null,
            'max' => strlen($value ?? '') > (int) $parameter ? "The {$field} may not be greater than {$parameter} characters." : null,
            'confirmed' => $value !== ($data[$field . '_confirmation'] ?? null) ? "The {$field} confirmation does not match." : null,
            'unique' => $this->isUnique($field, $value, $parameter) ? null : "The {$field} has already been taken.",
            default => null,
        };
    }

    private function isUnique(string $field, mixed $value, ?string $table): bool
    {
        if (!$table || !$value) {
            return true;
        }

        return true;
    }
}