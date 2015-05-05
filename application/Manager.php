<?php
namespace application;

/**
 * Class Manager
 * @package application
 */
class Manager
{
    const CONFIG_KEY_PASSWORD_RULES     = 'passwordRules';
    const CONFIG_RULES_KEY_MESSAGE      = 'message';
    const CONFIG_RULES_KEY_REGEXP       = 'regexp';
    const CONFIG_RULES_KEY_RULES        = 'rules';
    const CONFIG_KEY_FILE_PATH          = 'filepath';


    /**
     * @var \application\model\Password
     */
    protected $passwordModel;

    /**
     * @var array
     */
    public static $config;

    /**
     * Initializes Password Model
     */
    public function __construct()
    {
        $this->passwordModel = new \application\model\Password(
            new \application\Database\Db()
        );
    }

    /**
     * Retrieves configuration array
     * @return array
     */
    public static function getConfig()
    {
        if (!self::$config) {
            self::$config = require('configuration/config.php');
        }
        return self::$config;
    }

    /**
     * Main Processing method
     * - resets database before the new check
     * - retrieves the list of passwords to check
     * - checks above list
     * - updates database for successful checks
     *
     * @return bool whether the database update was successful
     */
    public function processPasswords()
    {
        $validPasswords = [];
        $this->passwordModel->setAllInvalid();

        $passwords = $this->retrieveListOfPasswords();
        if (empty($passwords)) {
            echo $this->getEmptyPasswordListMessage();
            return false;
        }

        echo $this->getInitialMessage();

        $passwordChecker = new \application\checker\PasswordChecker(
            $this->getValidationRules(self::getConfig()[self::CONFIG_KEY_PASSWORD_RULES][self::CONFIG_KEY_FILE_PATH])
        );

        foreach ($passwords as $id => $password) {
            if ($passwordChecker->check($password)) {
                $validPasswords[] = $id;
            }
        }
        
        if (!empty($validPasswords)) {
            return $this->passwordModel->setValidByIds($validPasswords);
        }
        
        return true;
    }

    /**
     * Retrieves from database the list of passwords and returns the result in array format
     *
     * @return array
     */
    protected function retrieveListOfPasswords()
    {
        $passwords = [];
        $results = $this->passwordModel->fetchAll();
        if ($results) {
            foreach ($results as $row) {
                $passwords[$row[\application\model\Password::COLUMN_NAME_ID]]
                                = $row[\application\model\Password::COLUMN_NAME_PASSWORD];
            }
        }
        return $passwords;
    }

    /**
     * @return string
     */
    protected function getInitialMessage()
    {
        return "\nProcessing Password List .....\n\n";
    }

    /**
     * @return string
     */
    protected function getEmptyPasswordListMessage()
    {
        return "\nThe password list was found empty\n";
    }

    /**
     * Reads rules from yaml file, validates, and creates array of ValidationRule objects
     * to be injected in Checker class
     * @param string $filePath
     * @return \application\checker\ValidationRule[]
     */
    protected function getValidationRules($filePath)
    {
        try {
            $reader = new \application\yaml\Reader($filePath);
            $rulesArray = $reader->getContent();
            $this->validateRulesArray($rulesArray);
            return $this->createValidationRules($rulesArray);
        } catch (\Exception $ex) {
            echo 'Error Creating Validation Rules: ' . $ex->getMessage() . "\n";
            exit;
        }
    }

    /**
     * Creates array of ValidationRule objects from input config array
     * @param array $rulesArray
     * @return \application\checker\ValidationRule[]
     */
    protected function createValidationRules(array $rulesArray)
    {
        $rules = [];
        foreach ($rulesArray[self::CONFIG_RULES_KEY_RULES] as $rule) {
            $rules[] = new \application\checker\ValidationRule(
                $rule[self::CONFIG_RULES_KEY_REGEXP], $rule[self::CONFIG_RULES_KEY_MESSAGE]
            );
        }
        return $rules;
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
    protected function validateRulesArray(array &$rules)
    {
        if (
            empty($rules)
            || !array_key_exists(self::CONFIG_RULES_KEY_RULES, $rules)
            || empty($rules[self::CONFIG_RULES_KEY_RULES])

        ) {
            throw new \Exception('Password Checker could not function with an empty set of rules.');
        }

        foreach ($rules[self::CONFIG_RULES_KEY_RULES] as $key => $rule) {
            if (
                !array_key_exists(self::CONFIG_RULES_KEY_REGEXP, $rule) ||
                !array_key_exists(self::CONFIG_RULES_KEY_MESSAGE, $rule)
            ) {
                unset($rules[self::CONFIG_RULES_KEY_RULES][$key]);
            }
        }

        if (empty($rules[self::CONFIG_RULES_KEY_RULES])) {
            throw new \Exception(
                'Password Checker could not function with an empty set of rules. The syntax of the rules file is not correct'
            );
        }
    }

} 