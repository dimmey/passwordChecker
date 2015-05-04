<?php
namespace application\yaml;

/**
 * Class Reader
 * @package application\yaml
 */
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
            if (!function_exists('yaml_parse_file')) {
                throw new \Exception('PHP yaml extension is not installed. Please install extension before executing.');
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