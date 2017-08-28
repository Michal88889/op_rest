<?php

namespace RestApp\Request;

//Exceptions
use RestApp\Exceptions\Validation\ParamsException;

/*
 * Custom validator for array params validation
 */

class Validator {

    /**
     * Acceptable rules
     * @var mixed[]
     */
    protected $_rules = [
        'isset', 'isNumeric', 'isNotEmpty', '>x', '<x', '=x'
    ];

    /**
     * Rules set by user
     * @var mixed[] 
     */
    protected $rules = [];

    /**
     * Validation error messages
     * @var mixed[] 
     */
    protected $errorMessages = [];

    /**
     * Array for validation
     * @var mixed[] 
     */
    protected $toValidate = [];

    /**
     * Set array to validate in constructor
     * @param mixed[] $toValidate
     */
    public function __construct($toValidate = []) {
        $this->setArray($toValidate);
    }

    /**
     * Set array to validate
     * @param mixed[] $toValidate
     */
    public function setArray($toValidate) {
        if (is_array($toValidate) && !empty($toValidate)) {
            $this->toValidate = $toValidate;
        }
    }

    /**
     * Add validation rules to certain array field
     * @param string $field
     * @param mixed[] $rules
     * @throws ParamsException
     * @return Validator
     */
    public function addRules($field, $rules) {
        if ($this->areRulesAcceptable($rules)) {
            if ($this->rulesExist($field)) {
                $this->rules[$field] = array_merge($this->rules[$field], $rules);
            } else {
                $this->rules[$field] = $rules;
            }
        } else {
            throw new ParamsException('Unsupported validation rules for field: ' . $field);
        }
    }

    /**
     * Validate all fields
     * @return boolean
     */
    public function validate() {
        $valid = true;
        if (is_array($this->toValidate) && !empty($this->toValidate)) {
            foreach ($this->toValidate as $fieldName => $fieldValue) {
                if (!$this->validateField($fieldName)) {
                    $valid = false;
                    break;
                }
            }
        }
        return $valid;
    }

    /**
     * Get error messages
     * @return mixed[]
     */
    public function getErrorMessages() {
        return $this->errorMessages;
    }

    /**
     * Get first error message
     * @return string|boolean
     */
    public function getFirstErrorMessage() {
        if (!empty($this->errorMessages)) {
            return $this->errorMessages[0];
        } else {
            return false;
        }
    }

    /**
     * Validate field
     * @param string $field
     * @return boolean
     */
    protected function validateField($field) {
        $valid = true;
        if ($this->rulesExist($field)) {
            foreach ($this->rules[$field] as $rule) {
                if (!$this->validateRule($field, $rule)) {
                    $valid = false;
                    break;
                }
            }
        }
        return $valid;
    }

    /**
     * Validate single for field
     * @param string $field
     * @param string $rule
     * @return boolean
     */
    protected function validateRule($field, $rule) {
        switch ($rule) {
            case 'isset':
                return $this->verify(isset($this->toValidate[$field]), 'Field ' . $field . ' do not exist');
            case 'isNumeric':
                return $this->verify(is_numeric($this->toValidate[$field]), 'Field ' . $field . ' value must be numeric');
            case 'isNotEmpty':
                return $this->verify((string) $this->toValidate[$field] !== '', 'Field ' . $field . ' value can not be empty');
            default:
                //in case of >, <, =
                $ruleType = substr($rule, 0, 1);
                $ruleParam = substr($rule, 1, strlen($rule) - 1);
                switch ($ruleType) {
                    case '>':
                        return $this->verify($this->toValidate[$field] > $ruleParam, 'Field ' . $field . ' value must be greater than ' . $ruleParam);
                    case '<':
                        return $this->verify($this->toValidate[$field] < $ruleParam, 'Field ' . $field . ' value must be lower than ' . $ruleParam);
                    case '=':
                        return $this->verify($this->toValidate[$field] == $ruleParam, 'Field ' . $field . ' value must equal ' . $ruleParam);
                }
        }
    }

    /**
     * Varify condition and save error
     * @param boolean $valid
     * @param string $msg
     * @return boolean
     */
    protected function verify($valid, $msg) {
        if (!$valid) {
            array_push($this->errorMessages, $msg);
        }
        return $valid;
    }

    /**
     * Check if rule exist
     * @param string $field
     * @return boolean
     */
    protected function rulesExist($field) {
        return isset($this->rules[$field]);
    }

    /**
     * Check if given rules are acceptable
     * @param mixed[] $rules
     * @return boolean
     */
    protected function areRulesAcceptable($rules) {
        foreach ($rules as $rule) {
            if (!in_array($rule, $this->_rules) && !in_array(substr($rule, 0, 1), ['>', '<', '='])) {
                return false;
            }
        }
        return true;
    }

}
