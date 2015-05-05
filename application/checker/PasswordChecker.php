<?php
namespace application\checker;

/**
 * Class PasswordChecker
 * @package application\checker
 */
class PasswordChecker
{
    /**
     * @var \application\checker\ValidationRule[]
     */
    protected $rules;

    /**
     * Upon construction the set of rules is injected to the class
     *
     * @param array $rules
     */
    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @param array $rules
     */
    public function setRules(array $rules) {
        $this->rules = $rules;
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
     * @param \application\checker\ValidationRule $rule
     * @return bool
     */
    protected function checkAgainstRule($password, \application\checker\ValidationRule $rule) {
        if (preg_match($rule->getRegularExpression(), $password)) {
            return true;
        } else {
            echo $this->getErrorMessage($password, $rule->getErrorMessage());
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
} 