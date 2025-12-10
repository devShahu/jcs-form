<?php

namespace App;

class ValidationResult
{
    public bool $isValid;
    public array $errors;
    public array $sanitizedData;

    public function __construct(bool $isValid, array $errors = [], array $sanitizedData = [])
    {
        $this->isValid = $isValid;
        $this->errors = $errors;
        $this->sanitizedData = $sanitizedData;
    }
}

class FormValidator
{
    private array $formFields;
    private array $errors = [];

    public function __construct()
    {
        $this->formFields = require __DIR__ . '/../../config/form_fields.php';
    }

    /**
     * Validate form data
     */
    public function validate(array $data): ValidationResult
    {
        $this->errors = [];
        $sanitizedData = $this->sanitize($data);

        // Validate personal info
        $this->validateSection($sanitizedData, 'personal_info');
        
        // Validate contact info
        $this->validateSection($sanitizedData, 'contact_info');
        
        // Validate address info
        $this->validateSection($sanitizedData, 'address_info');
        
        // Validate education
        $this->validateEducation($sanitizedData);
        
        // Validate declaration
        $this->validateSection($sanitizedData, 'declaration');

        $isValid = empty($this->errors);

        return new ValidationResult($isValid, $this->errors, $sanitizedData);
    }

    /**
     * Validate a specific step
     */
    public function validateStep(string $step, array $data): ValidationResult
    {
        $this->errors = [];
        $sanitizedData = $this->sanitize($data);

        error_log("Validating step: $step");

        switch ($step) {
            case 'personal':
                $this->validateSection($sanitizedData, 'personal_info');
                $this->validateSection($sanitizedData, 'contact_info');
                $this->validateSection($sanitizedData, 'address_info');
                $this->validateSection($sanitizedData, 'background_info');
                break;
            case 'education':
                $this->validateEducation($sanitizedData);
                break;
            case 'declaration':
                $this->validateSection($sanitizedData, 'declaration');
                break;
            default:
                error_log("Unknown step: $step");
                break;
        }

        error_log("Validation errors for step $step: " . json_encode($this->errors));

        $isValid = empty($this->errors);
        return new ValidationResult($isValid, $this->errors, $sanitizedData);
    }

    /**
     * Sanitize input data
     */
    public function sanitize(array $data): array
    {
        $sanitized = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = $this->sanitize($value);
            } elseif (is_string($value)) {
                // Remove HTML tags and encode special characters
                $sanitized[$key] = htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Get validation errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Validate a section of the form
     */
    private function validateSection(array $data, string $section): void
    {
        if (!isset($this->formFields[$section])) {
            error_log("Section not found: $section");
            return;
        }

        foreach ($this->formFields[$section] as $fieldName => $fieldConfig) {
            $value = $data[$fieldName] ?? null;
            error_log("Validating field: $fieldName, value: " . ($value ?? 'null'));
            $this->validateField($fieldName, $value, $fieldConfig);
        }
    }

    /**
     * Validate education section
     */
    private function validateEducation(array $data): void
    {
        if (!isset($this->formFields['education'])) {
            return;
        }

        foreach ($this->formFields['education'] as $level => $config) {
            error_log("Validating education level: $level");
            foreach ($config['fields'] as $fieldKey => $fieldConfig) {
                // Use the 'name' property from config, not the array key
                $fieldName = $fieldConfig['name'] ?? $fieldKey;
                $value = $data[$fieldName] ?? null;
                error_log("Education field: $fieldName = " . ($value ?? 'null'));
                $this->validateField($fieldName, $value, $fieldConfig);
            }
        }
    }

    /**
     * Validate a single field
     */
    private function validateField(string $fieldName, $value, array $config): void
    {
        $rules = $config['validation'] ?? [];
        $required = $config['required'] ?? false;

        // Skip validation if field is not required and not provided
        if (!$required && ($value === null || $value === '')) {
            return;
        }

        foreach ($rules as $rule) {
            if (is_string($rule)) {
                $this->applyRule($fieldName, $value, $rule, $config);
            }
        }
    }

    /**
     * Apply a validation rule
     */
    private function applyRule(string $fieldName, $value, string $rule, array $config): void
    {
        // Required rule
        if ($rule === 'required') {
            if ($value === null || $value === '' || $value === []) {
                $this->errors[$fieldName] = $config['label'] . ' আবশ্যক';
                return;
            }
        }

        // If field is empty and not required, skip other validations
        if ($value === null || $value === '') {
            return;
        }

        // Min length
        if (str_starts_with($rule, 'min:')) {
            $min = (int)substr($rule, 4);
            if (mb_strlen($value, 'UTF-8') < $min) {
                $this->errors[$fieldName] = $config['label'] . ' কমপক্ষে ' . $min . ' অক্ষর হতে হবে';
            }
        }

        // Max length
        if (str_starts_with($rule, 'max:')) {
            $max = (int)substr($rule, 4);
            if (mb_strlen($value, 'UTF-8') > $max) {
                $this->errors[$fieldName] = $config['label'] . ' সর্বোচ্চ ' . $max . ' অক্ষর হতে পারে';
            }
        }

        // Numeric
        if ($rule === 'numeric') {
            if (!is_numeric($value)) {
                $this->errors[$fieldName] = $config['label'] . ' সংখ্যা হতে হবে';
            }
        }

        // Digits
        if (str_starts_with($rule, 'digits:')) {
            $digits = (int)substr($rule, 7);
            if (!preg_match('/^\d{' . $digits . '}$/', $value)) {
                $this->errors[$fieldName] = $config['label'] . ' ' . $digits . ' সংখ্যার হতে হবে';
            }
        }

        // Email
        if ($rule === 'email') {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $this->errors[$fieldName] = 'সঠিক ইমেইল ঠিকানা দিন';
            }
        }

        // Date
        if ($rule === 'date') {
            $date = \DateTime::createFromFormat('Y-m-d', $value);
            if (!$date || $date->format('Y-m-d') !== $value) {
                $this->errors[$fieldName] = 'সঠিক তারিখ দিন (YYYY-MM-DD)';
            }
        }

        // Before today
        if ($rule === 'before:today') {
            $date = \DateTime::createFromFormat('Y-m-d', $value);
            $today = new \DateTime();
            if ($date && $date >= $today) {
                $this->errors[$fieldName] = 'তারিখ আজকের আগে হতে হবে';
            }
        }

        // Regex pattern
        if (str_starts_with($rule, 'regex:')) {
            $pattern = substr($rule, 6);
            if (!preg_match($pattern, $value)) {
                $this->errors[$fieldName] = $config['label'] . ' সঠিক ফরম্যাটে দিন';
            }
        }

        // Uppercase
        if ($rule === 'uppercase') {
            if ($value !== strtoupper($value)) {
                $this->errors[$fieldName] = $config['label'] . ' বড় হাতের অক্ষরে লিখুন';
            }
        }

        // Image validation
        if ($rule === 'image') {
            // This will be handled by PhotoProcessor
            // Just check if it's a valid base64 or file path
            if (!$this->isValidImageData($value)) {
                $this->errors[$fieldName] = 'সঠিক ছবি আপলোড করুন';
            }
        }

        // Accepted (for checkboxes)
        if ($rule === 'accepted') {
            if ($value !== true && $value !== 'true' && $value !== '1' && $value !== 1) {
                $this->errors[$fieldName] = 'আপনাকে এই শর্তে সম্মত হতে হবে';
            }
        }
    }

    /**
     * Check if value is valid image data
     */
    private function isValidImageData($value): bool
    {
        if (empty($value)) {
            return false;
        }

        // Check if it's a base64 encoded image
        if (preg_match('/^data:image\/(jpeg|png|jpg);base64,/', $value)) {
            return true;
        }

        // Check if it's a file path
        if (is_string($value) && file_exists($value)) {
            return true;
        }

        return false;
    }
}
