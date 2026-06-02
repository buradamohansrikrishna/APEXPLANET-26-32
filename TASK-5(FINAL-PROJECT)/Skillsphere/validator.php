<?php
// =========================================
// SKILLSPHERE VALIDATION HELPER
// validator.php
// =========================================

class Validator {
    private $errors = [];

    public function validateEmail($email, $name = 'email') {
        if (empty($email)) {
            $this->errors[$name] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$name] = 'Invalid email address format';
        }
        return $this;
    }

    public function validatePassword($password, $minLength = 6, $name = 'password') {
        if (empty($password)) {
            $this->errors[$name] = 'Password is required';
        } elseif (strlen($password) < $minLength) {
            $this->errors[$name] = "Password must be at least {$minLength} characters long";
        }
        return $this;
    }

    public function validateRequired($value, $fieldName, $errorMessage = '') {
        if (empty(trim($value))) {
            $this->errors[$fieldName] = !empty($errorMessage) ? $errorMessage : ucfirst($fieldName) . ' is required';
        }
        return $this;
    }

    public function validateMatch($val1, $val2, $fieldName, $errorMessage) {
        if ($val1 !== $val2) {
            $this->errors[$fieldName] = $errorMessage;
        }
        return $this;
    }

    public function validatePhone($phone, $name = 'phone') {
        if (!empty($phone) && !preg_match('/^[0-9\-\+\s\(\)]{8,20}$/', $phone)) {
            $this->errors[$name] = 'Invalid phone number format';
        }
        return $this;
    }

    public function hasErrors() {
        return !empty($this->errors);
    }

    public function getErrors() {
        return $this->errors;
    }
}
?>
