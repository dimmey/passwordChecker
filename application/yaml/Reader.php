<?php

namespace application\yaml;

class Reader
{
    /**
     * @var array
     */
    protected $content;

    /**
     * @param string $filePath
     */
    public function __construct($filePath)
    {
        try {
            if (!is_file(realpath($filePath))) {
                throw new \Exception('Invalid filepath: ' . $filePath);
            }
            $this->content = yaml_parse_file(realpath($filePath));
        } catch (\Exception $ex) {
            echo 'Could not read yaml file. Error: ' . $ex->getMessage() . "\n";
            die;
        }
    }

    /**
     * @return array
     */
    public function getContent()
    {
        return $this->content;
    }
} 