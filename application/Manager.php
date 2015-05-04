<?php
namespace application;

class Manager
{
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
        $this->connection = new \application\Database\Db();
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
     * - updates database with
     *
     * @return bool whether the database update was successful
     */
    public function processPasswords()
    {
        $validPasswords = [];
        $this->resetDatabase();
        $passwords = $this->retrieveListOfPasswords();
        $passwordChecker = new \application\checker\PasswordChecker(
                self::getConfig()['passwordRules']['filepath']
        );

        foreach ($passwords as $id => $password) {
            if ($passwordChecker->check($password)) {
                $validPasswords[] = $id;
            }
        }
        return $this->updateDatabaseWithValidPasswords($validPasswords);
    }

    /**
     * Retrieves from database the list of passwords and returns the result in array format
     *
     * @return array
     */
    protected function retrieveListOfPasswords()
    {
        $passwords = [];
        $results = $this->connection->query('SELECT id, password FROM `passwords`');
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
        return $this->connection->query('UPDATE `passwords` SET `valid`=0');
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

        return $this->connection->prepareAndExecute(
            "UPDATE `passwords` SET `valid`=1 WHERE `id` IN ($placeholders)",
            $ids
        );
    }

} 