<?php

namespace Configuration;

class Repository {

    /**
     * The instance of the singleton
     *
     * @var self
     */
    private static $instance = null;

    /**
     * All of the configuration items.
     *
     * @var array
     */
    protected $items = [];
        
    /**
     * Private constructor.
     *
     * @return self
     */
    private function __construct() { }

    /**
     * Prevent obeject coloning.
     *
     */
    private function __clone() { }

    /**
     * Prevent object unserialization.
     *
     */
    private function __wakeup() { }

    /**
     * Get the repository instance via lazy initialization 
     *
     * @return self
     */
    public static function getInstance() {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param string $key
     * @return bool
     */
    public function has($key) {
        return array_has($this->items, $key);        
    }
    
    /**
     * Get the specified configuration value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null) {
        return array_get($this->items, $key, $default);
    }
    
    /**
     * Get all of the configuration items for the application.
     *
     * @return array
     */
    public static function all() {
        return $this->items;
    }
    
    /**
     * Set the given configuration values.
     *
     * @param array|string $key
     * @param mixed|null $value
     * @return void
     */
    public function set($key, $value = null) {
        $keys = is_array($key) ? $key : [$key => $value];

        foreach($keys as $key => $value) {
            array_set($this->items, $key, $value);
        }
    }
    
    /**
     * Prepend a value onto an array configuration value.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function prepend($key, $value) {
        $array = $this->get($key);
        
        array_unshift($array, $value);

        $this->set($key, $array);
    }

    /**
     * Push a value onto an array configuration value.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function push($key, $value) {
        $array = $this->get($key);

        $array[] = $value;

        $this->set($key, $array);
    }

    /**
     * Set the base path for the application.
     *
     * @param string $value
     * @return void
     */
    public function setBasePath($value) {
        $this->set('base_path', $value);
    }

    /**
     * Get the base path for the application.
     *
     * @return string
     */
    public function getBasePath() {
        return $this->get('base_path');
    }

    /**
     * Load a/all configuration file(s) into the repository.
     *
     * @param string|null $name
     * @return void
     */
    public function load($name = null) {
        if (!$name) {
            $path = $this->getConfigPath();
            $configFiles = preg_grep("/.+\.php$/", scandir($path));
            
            foreach ($configFiles as $configFile) {
                if (preg_match("/(.+)\.php$/", $configFile, $matches) && $matches[1]) {
                    $this->load($matches[1]);
                }
            }
        } else {
            if (isset($this->items[$name])) {
                return;
            }

            $path = $this->getConfigPath($name);
            if ($path) {
                $this->items[$name] = require $path;
            }
        }
    }
    
    /**
     * Get the path to the given configuration directory/file.
     *
     * @param string|null $name
     * @return string
     */
    protected function getConfigPath($name = null) {
        if (!$name) {
            if ($this->getBasePath() && file_exists(
                $path = $this->getBasePath().'/config/')) {
                return $path;
            }
            elseif (file_exists($path = __DIR__.'/../config/')) {
                return $path;
            }
        } elseif ($this->getBasePath() && file_exists(
            $path = $this->getBasePath().'/config/'.$name.'.php')) {
            return $path;
        } elseif (file_exists($path = __DIR__.'/../config/'.$name.'.php')) {
            return $path;
        }
    }
    
    /**
     * Handle dynamic static calls to the object.
     *
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public static function __callStatic($method, $args) {
        $instance = static::getInstance();

        return call_user_func_array([$instance, $method], $args);
    }
}
