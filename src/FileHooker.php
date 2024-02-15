<?php namespace Nabeghe\FileHooker;

/**
 * Hooking System based on files.
 *
 * @version 1.0.0
 * @author Hadi Akbarzadeh <HadiCoder@gmail.com>
 * @package Nabeghe\FileHooker
 * @link https://github.com/nabeghe/php-file-hooker
 * @license https://opensource.org/licenses/MIT
 */
class FileHooker
{
    /**
     * The second object of all callbacks after data.
     * @var mixed
     */
    protected $angler;

    /**
     * The root paths where the hooks are located.
     * @var array
     */
    protected array $paths = [];

    /**
     * Is it frozen?
     * @var bool
     */
    protected bool $freezed = false;

    /**
     * Running hooks.
     * @var array
     */
    protected array $hookings = [];

    /**
     * @return array
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * Freeze to prevent hooks from running.
     * @return void
     */
    public function freez()
    {
        $this->freezed = true;
    }

    /**
     * Unfreeze to enable the execution of hooks.
     * @return void
     */
    public function unfreez()
    {
        $this->freezed = false;
    }

    /**
     * @param  mixed  $agnler
     */
    public function __construct($agnler = null)
    {
        $this->angler = $agnler;
    }

    /**
     * Adds a new path where the hooks are located.
     * @param  string  $path  Path of Hooks.
     * @param  mixed  $id  Optional. Unique Identifier. Default null (without identifier).
     * @return void
     */
    public function add(string $path, $id = null)
    {
        foreach ($this->paths as $_id => $_path) {
            if ($path == $_path) {
                return;
            }
        }

        if (func_num_args() == 1) {
            $this->paths[] = $path;
        } else {
            $this->paths[$id] = $path;
        }
    }

    /**
     * Sends a data to the hooks without any return.
     * @param  string  $hook  Hook name.
     * @param  array  $data  Optional data.
     * @return void
     */
    public function action(string $hook, $data = [], &$refData = [])
    {
        if (!$this->freezed && !in_array($hook, $this->hookings)) {
            $this->hookings[] = $hook;
            foreach ($this->paths as $id => $path) {
                if (file_exists("$path/$hook.php")) {
                    $callable = include "$path/$hook.php";
                    $callable($data, $this->angler);
                }
            }
            if (func_num_args() > 2) {
                $refData = $data;
            }
            $this->hookings = array_diff($this->hookings, [$hook]);
        }
    }

    /**
     * Sends a data to the hooks which finally returns only index 0 of data.
     * In fact, the index 0 is the value that is filtered.
     * @param  string  $hook  Hook name.
     * @param  array  $data  Filtered data and other arguments.
     * @return mixed The value of index 0, null if not present.
     */
    public function filter(string $hook, array $data = [])
    {
        if (!$this->freezed && !in_array($hook, $this->hookings)) {
            $this->hookings[] = $hook;
            foreach ($this->paths as $path) {
                if (file_exists("$path/$hook.php")) {
                    $callable = include "$path/$hook.php";
                    if ($callable) {
                        $data = $callable($data, $this->angler);
                    }
                }
            }
            $this->hookings = array_diff($this->hookings, [$hook]);
        }
        return $data[0];
    }
}