<?php namespace Nabeghe\FileHooker;

/**
 * Hooking System based on files.
 *
 * @author Hadi Akbarzadeh <elsiomcoder@gmail.com>
 * @package Nabeghe\FileHooker
 * @link https://github.com/nabeghe/php-file-hooker
 * @license https://opensource.org/licenses/MIT
 */
class FileHooker
{
    /**
     * The second object of all callbacks after data.
     *
     * @var mixed
     */
    protected $angler;

    /**
     * The root paths where the hooks are located.
     *
     * @var array
     */
    protected array $paths = [];

    /**
     * Is it frozen?
     *
     * @var bool
     */
    protected bool $freezed = false;

    /**
     * Running hooks.
     *
     * @var array
     */
    protected array $hookings = [];

    /**
     * Is cacheable or not?
     *
     * @var bool
     */
    protected bool $cacheable = false;

    /**
     * Loaded hooks from files.
     *
     * @var array|null
     */
    protected array $cache = [];

    /**
     * Constructor.
     *
     * @param  mixed  $agnler  Optional. The second object of all callbacks after data. Default null.
     * @param  bool  $cacheable  Optional. Is cacheable or not? Default false.
     */
    public function __construct($agnler = null, bool $cacheable = false)
    {
        $this->angler = $agnler;
        $this->cacheable = $cacheable;
    }

    public function getPaths(): array
    {
        return $this->paths;
    }

    /**
     * Freeze to prevent hooks from running.
     */
    public function freez(): void
    {
        $this->freezed = true;
    }

    /**
     * Unfreeze to enable the execution of hooks.
     */
    public function unfreez(): void
    {
        $this->freezed = false;
    }

    /**
     * Delete cache.
     *
     * @param  mixed  $pathId  Optional. Id of a path. Default null (all paths).
     */
    public function decache($pathId = null): void
    {
        if (is_null($pathId)) {
            $this->cache = [];
        } elseif (isset($this->cache[$pathId])) {
            unset($this->cache[$pathId]);
        }
    }

    /**
     * Adds a new path where the hooks are located.
     *
     * @param  string  $path  Path of Hooks.
     * @param  mixed  $id  Optional. Unique Identifier. Default null (without identifier).
     */
    public function add(string $path, $id = null): void
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
     *
     * @param  string  $hook  Hook name.
     * @param  array  $data  Optional data.
     */
    public function action(string $hook, $data = [], &$refData = []): void
    {
        if (!$this->freezed && !in_array($hook, $this->hookings)) {
            $this->hookings[] = $hook;

            foreach ($this->paths as $id => $path) {
                $callable = null;

                if ($this->cacheable && isset($this->cache[$id]) && $this->cache[$id][$hook]) {
                    $callable = $this->cache[$id][$hook]($data, $this->angler);
                }

                if (!$callable && file_exists("$path/$hook.php")) {
                    $callable = include "$path/$hook.php";
                    if ($this->cacheable) {
                        if (!isset($this->cache[$id])) {
                            $this->cache[$id] = [];
                        }
                        $this->cache[$id][$hook] = $callable;
                    }
                }

                if ($callable) {
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
     *
     * @param  string  $hook  Hook name.
     * @param  array  $data  Filtered data and other arguments.
     * @return mixed The value of index 0, null if not present.
     */
    public function filter(string $hook, array $data = [])
    {
        if (!$this->freezed && !in_array($hook, $this->hookings)) {
            $this->hookings[] = $hook;

            foreach ($this->paths as $id => $path) {
                $callable = null;

                if ($this->cacheable && isset($this->cache[$id]) && $this->cache[$id][$hook]) {
                    $callable = $this->cache[$id][$hook]($data, $this->angler);
                }

                if (!$callable && file_exists("$path/$hook.php")) {
                    $callable = include "$path/$hook.php";
                    if ($this->cacheable) {
                        if (!isset($this->cache[$id])) {
                            $this->cache[$id] = [];
                        }
                        $this->cache[$id][$hook] = $callable;
                    }
                }

                if ($callable) {
                    $data = $callable($data, $this->angler);
                }
            }

            $this->hookings = array_diff($this->hookings, [$hook]);
        }

        return $data[0];
    }
}