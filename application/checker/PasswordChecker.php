<?php
namespace application\checker;

class PasswordChecker
{
    const RULES_KEY_MESSAGE = 'message';
    const RULES_KEY_REGEXP  = 'regexp';
    const RULES_KEY_RULES   = 'rules';

    /**
     * @var array
     */
    protected $rules;

    /**
     * Upon construction the set of rules are injected to the class
     *
     * @param string $filePath
     */
    public function __construct($filePath)
    {
        try {
            $reader = new \application\yaml\Reader($filePath);
            $rules = $reader->getContent();
            $this->validateRules($rules);
            $this->rules = $rules;
        } catch (\Exception $ex) {
            echo 'Error Initiating ' .__CLASS__ . ' : ' . $ex->getMessage() . "\n";
            exit;
        }
    }

    /**
     * @return array
     */
    protected function getRules()
    {
        return $this->rules[self::RULES_KEY_RULES];
    }

    /**
     * Checks input password against the class' set of password checking rules
     *
     * @param string $password
     * @return bool
     */
    public function check($password)
    {
        foreach ($this->getRules() as $rule) {
            if (!$this->checkAgainstRule($password, $rule)) {
                return false;
            }
        }
        echo $this->getSuccessMessage($password);
        return true;
    }

    /**
     * Checks input password against specific rule
     *
     * @param string $password
     * @param array $rule
     * @return bool
     */
    protected function checkAgainstRule($password, array $rule) {
        if (preg_match($rule[self::RULES_KEY_REGEXP], $password)) {
            return true;
        } else {
            echo $this->getErrorMessage($password, $rule[self::RULES_KEY_MESSAGE]);
            return false;
        }
    }

    /**
     * Creates success message to be output in case password check is successful
     *
     * @param string $password
     * @return string
     */
    protected function getSuccessMessage($password)
    {
        return "[SUCCESS]: Password '$password' is valid\n";
    }

    /**
     * Creates error message to be output in case the check is not successful
     *
     * @param string $password
     * @param string $message
     * @return string
     */
    protected function getErrorMessage($password, $message)
    {
        return "[FAILURE]: Password '$password' is not valid. Error: $message\n";
    }

    /**
     * Processing input rule array, as being retrieved from yaml file
     * The rules in the input array are validated for correct structure, and only the ones that
     * pass the validation are finally kept to be used
     *
     * @param array $rules
     * @throws \Exception
     * @return void
     */
    protected function validateRules(array &$rules)
    {
        if (
            empty($rules)
            || !array_key_exists(self::RULES_KEY_RULES, $rules)
            || empty($rules[self::RULES_KEY_RULES])

        ) {
            throw new \Exception('Password Checker could not function with an empty set of rules.');
        }

        foreach ($rules[self::RULES_KEY_RULES] as $key => $rule) {
            if (
                !array_key_exists(self::RULES_KEY_REGEXP, $rule) ||
                !array_key_exists(self::RULES_KEY_MESSAGE, $rule)
            ) {
                unset($rules[self::RULES_KEY_RULES][$key]);
            }
        }

        if (empty($rules[self::RULES_KEY_RULES])) {
            throw new \Exception(
                'Password Checker could not function with an empty set of rules. The syntax of the rules file is not correct'
            );
        }
    }
} 