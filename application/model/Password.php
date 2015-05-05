<?php
namespace application\model;

/**
 * Class Password
 * @package application\model
 */
class Password
{
    const TABLE_NAME            = 'passwords';
    const COLUMN_NAME_ID        = 'id';
    const COLUMN_NAME_PASSWORD  = 'password';
    const COLUMN_NAME_VALID     = 'valid';
    const VALUE_VALID_YES       = 1;
    const VALUE_VALID_NO        = 0;


    /**
     * @var \application\Database\Db
     */
    protected $db;

    /**
     * @param \application\Database\Db $db
     */
    public function __construct(\application\Database\Db $db)
    {
        $this->db = $db;
    }

    /**
     * Retrieves all contents of table
     *
     * @return \PDOStatement
     */
    public function fetchAll()
    {
        return $this->db->query('SELECT * FROM ' .self::TABLE_NAME);
    }

    /**
     * Sets all passwords in database as invalid / uncheked
     *
     * @return \PDOStatement
     */
    public function setAllInvalid()
    {
        return $this->db->query(
            'UPDATE `' . self::TABLE_NAME . '` SET `' . self::COLUMN_NAME_VALID . '`=' . self::VALUE_VALID_NO
        );
    }

    /**
     * Updates all database rows by id and sets the column valid
     *
     * @param array $ids
     * @return bool
     */
    public function setValidByIds(array $ids)
    {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        return $this->db->prepareAndExecute(
            "UPDATE `" . self::TABLE_NAME . '` SET `' . self::COLUMN_NAME_VALID . '`=' . self::VALUE_VALID_YES .
            ' WHERE `' .self::COLUMN_NAME_ID . "` IN ($placeholders)",
            $ids
        );
    }

} 