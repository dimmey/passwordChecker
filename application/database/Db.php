<?php
namespace application\Database;

/**
 * Class Db
 * @package application\Database
 */
class Db
{
    const CONFIG_KEY_MYSQL      = 'mysql';
    const CONFIG_KEY_HOST       = 'host';
    const CONFIG_KEY_DATABASE   = 'database';
    const CONFIG_KEY_USERNAME   = 'username';
    const CONFIG_KEY_PASSWORD   = 'password';

    /**
     * @var array
     */
    protected $config;
    /**
     * @var \PDO
     */
    protected $connection;

    /**
     * Database Connection Initialization
     */
    public function __construct()
    {
        $this->config = \application\Manager::getConfig()[self::CONFIG_KEY_MYSQL];

        try {
            $this->connection = new \PDO(
                'mysql:host=' . $this->config[self::CONFIG_KEY_HOST] .
                ';dbname=' . $this->config[self::CONFIG_KEY_DATABASE] . ';charset=UTF8',
                $this->config[self::CONFIG_KEY_USERNAME],
                $this->config[self::CONFIG_KEY_PASSWORD],
                [\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'']
            );
        } catch (\PDOException $e) {
            echo "Error Connecting to DataBase!: " . $e->getMessage() . "\n";
            exit;
        }
    }

    /**
     * Executes input query
     *
     * @param string $sql
     * @return \PDOStatement
     */
    public function query($sql)
    {
        try {
            return $this->connection->query($sql, \PDO::FETCH_ASSOC);
        } catch (\PDOException $ex) {
            echo "Error Executing query: '$sql'. Message: " . $ex->getMessage() . "\n";
            exit;
        }
    }

    /**
     * Prepares statement and executes with parameters
     *
     * @param string $statement
     * @param array $params
     * @return bool
     */
    public function prepareAndExecute($statement, array $params)
    {
        try {
            $query = $this->connection->prepare($statement);
            if ($query->execute($params)) {
                return true;
            } else {
                return false;
            }
        } catch (\PDOException $ex) {
            echo "Error Executing query: '$statement'. Message: " . $ex->getMessage() . "\n";
            exit;
        }
    }
} 