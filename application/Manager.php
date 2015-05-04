<?php
namespace application;

/**
 * Class Manager
 * @package application
 */
class Manager
{
    const CONFIG_KEY_PASSWORD_RULES = 'passwordRules';
    const CONFIG_KEY_FILE_PATH      = 'filepath';

    /**
     * @var \application\Database\Db
     */
    protected $db;

    /**
     * @var array
     */
    public static $config;

    /**
     *
     */
    public function __construct()
    {
        $this->db = new \application\Database\Db();
    }

    /**
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
        $this->resetDatabase();
        $passwords = $this->retrieveListOfPasswords();
        if (empty($passwords)) {
            echo $this->getEmptyPasswordListMessage();
            return false;
        }

        echo $this->getInitialMessage();
        $passwordChecker = new \application\checker\PasswordChecker(
                self::getConfig()[self::CONFIG_KEY_PASSWORD_RULES][self::CONFIG_KEY_FILE_PATH]
        );

        foreach ($passwords as $id => $password) {
            if ($passwordChecker->check($password)) {
                $validPasswords[] = $id;
            }
        }
        
        if (!empty($validPasswords)) {
            return $this->updateDatabaseWithValidPasswords($validPasswords);    
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
        $results = $this->db->query('SELECT id, password FROM `passwords`');
        if ($results) {
            foreach ($results as $row) {
                $passwords[$row['id']] = $row['password'];
            }
        }
        return $passwords;
    }

    /**
     * Sets all passwords in database as unchecked
     *
     * @return \PDOStatement
     */
    protected function resetDatabase()
    {
        return $this->db->query('UPDATE `passwords` SET `valid`=0');
    }

    /**
     * Updates the valid passwords in database, after check
     *
     * @param array $ids
     * @return bool
     */
    protected function updateDatabaseWithValidPasswords(array $ids)
    {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        return $this->db->prepareAndExecute(
            "UPDATE `passwords` SET `valid`=1 WHERE `id` IN ($placeholders)",
            $ids
        );
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

} 