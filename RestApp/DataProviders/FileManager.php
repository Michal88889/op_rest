<?php

namespace RestApp\DataProviders;

//Exceptions
use Exception;
use RestApp\Exceptions\File\FileException;

/**
 * File manager responsible for proccesing Storage folder files
 */
class FileManager {

    /**
     * Storage directory
     * @var string 
     */
    protected $dir = '';

    /**
     * Storage config
     * @var mixed[]
     */
    protected $storageConfig = [];

    /**
     * Storage folder name
     * @var string
     */
    public $storageFolder = 'Storage';

    /**
     * Hide constructor, protected so only subclasses and self can use
     * @param string $dir
     * @param mixed[] $storage
     */
    public function __construct($dir, $storage) {
        $this->setDir($dir);
        $this->setStorageConfig($storage);
    }

    /**
     * Set dir
     * @param string $dir
     * @throws FileException
     */
    private function setDir($dir) {
        $this->dir = $dir . '/' . $this->storageFolder . '/';
        if (!file_exists($this->dir)) {
            throw new FileException('Given directory does not exist');
        }
    }

    /**
     * Set storage
     * @param mixed[] $storage
     * @throws FileException
     */
    private function setStorageConfig($storage) {
        if (is_array($storage) && !empty($storage)) {
            $this->storageConfig = $storage;
        } else {
            throw new FileException('Application storage is empty');
        }
    }

    /**
     * Get storage
     * @param string $storage
     * @return mixed[]
     * @throws FileException
     */
    public function getStorage($storage) {
        if (isset($this->storageConfig[$storage]) && file_exists($this->dir . $this->storageConfig[$storage])) {
            $file = fopen($this->dir . $this->storageConfig[$storage], "r");
            $members = [];
            while (!feof($file)) {
                $members[] = fgets($file);
            }

            fclose($file);
            return $members;
        } else {
            throw new FileException('Requested storage does not exist');
        }
    }

    /**
     * Save in storage
     * @param string $storage
     * @param string $text
     * @return FileManager
     * @throws FileException
     */
    public function saveInStorage($storage, $text) {
        if (isset($this->storageConfig[$storage]) && file_exists($this->dir . $this->storageConfig[$storage])) {
            try {
                file_put_contents($this->dir . $this->storageConfig[$storage], $text . PHP_EOL, FILE_APPEND);
            } catch (Exception $e) {
                throw new FileException($e->getMessage());
            }
            return $this;
        } else {
            throw new FileException('Requested storage does not exist');
        }
    }

}
