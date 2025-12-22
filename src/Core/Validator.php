<?php

namespace OmniPOS\Core;

class Validator
{
    protected array $data;
    protected array $rules;
    protected array $errors = [];
    protected array $messages = [
        'required' => 'El campo :field es obligatorio.',
        'numeric' => 'El campo :field debe ser numérico.',
        'min' => 'El campo :field debe ser mayor o igual a :min.',
        'max' => 'El campo :field debe ser menor o igual a :max.',
        'email' => 'El campo :field debe ser un correo válido.',
        'unique' => 'El valor del campo :field ya existe.',
        'positive' => 'El campo :field debe ser un número positivo.',
        'min_length' => 'El campo :field debe tener al menos :min caracteres.',
        'max_length' => 'El campo :field debe tener máximo :max caracteres.',
    ];

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    public function validate(): bool
    {
        foreach ($this->rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            foreach ($rules as $rule) {
                $this->applyRule($field, $rule);
            }
        }
        return empty($this->errors);
    }

    protected function applyRule(string $field, string $rule): void
    {
        $value = $this->data[$field] ?? null;
        
        // Parse rule with parameters (e.g., "min:5")
        $parts = explode(':', $rule);
        $ruleName = $parts[0];
        $params = $parts[1] ?? null;

        switch ($ruleName) {
            case 'required':
                if (empty($value) && $value !== '0') {
                    $this->addError($field, 'required');
                }
                break;

            case 'numeric':
                if ($value !== null && !is_numeric($value)) {
                    $this->addError($field, 'numeric');
                }
                break;

            case 'positive':
                if ($value !== null && (!is_numeric($value) || $value <= 0)) {
                    $this->addError($field, 'positive');
                }
                break;

            case 'min':
                if ($value !== null && is_numeric($value) && $value < (float)$params) {
                    $this->addError($field, 'min', ['min' => $params]);
                }
                break;

            case 'max':
                if ($value !== null && is_numeric($value) && $value > (float)$params) {
                    $this->addError($field, 'max', ['max' => $params]);
                }
                break;

            case 'email':
                if ($value !== null && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, 'email');
                }
                break;

            case 'min_length':
                if ($value !== null && strlen($value) < (int)$params) {
                    $this->addError($field, 'min_length', ['min' => $params]);
                }
                break;

            case 'max_length':
                if ($value !== null && strlen($value) > (int)$params) {
                    $this->addError($field, 'max_length', ['max' => $params]);
                }
                break;

            case 'unique':
                // Format: unique:table,column
                $uniqueParams = explode(',', $params);
                $table = $uniqueParams[0];
                $column = $uniqueParams[1] ?? $field;
                if (!$this->isUnique($table, $column, $value)) {
                    $this->addError($field, 'unique');
                }
                break;
        }
    }

    protected function isUnique(string $table, string $column, $value): bool
    {
        if ($value === null) return true;
        
        $pdo = \OmniPOS\Core\Database::connect();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM {$table} WHERE {$column} = :value");
        $stmt->execute(['value' => $value]);
        return $stmt->fetchColumn() == 0;
    }

    protected function addError(string $field, string $rule, array $params = []): void
    {
        $message = $this->messages[$rule] ?? "Validation failed for {$field}";
        $message = str_replace(':field', $field, $message);
        
        foreach ($params as $key => $val) {
            $message = str_replace(":{$key}", $val, $message);
        }
        
        $this->errors[$field][] = $message;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(string $field = null): ?string
    {
        if ($field) {
            return $this->errors[$field][0] ?? null;
        }
        
        foreach ($this->errors as $fieldErrors) {
            return $fieldErrors[0] ?? null;
        }
        
        return null;
    }

    public function failed(): bool
    {
        return !empty($this->errors);
    }
}
