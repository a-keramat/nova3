<?php namespace Nova\Extensions\Laravel\Config;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Config\LoaderInterface;
use Illuminate\Cache\FileStore as Cache;

class ConfigCascadingFileLoader implements LoaderInterface {

	/**
	 * The filesystem instance.
	 *
	 * @var	Illuminate\Filesystem
	 */
	protected $files;

	/**
	 * The default configuration path.
	 *
	 * @var	string
	 */
	protected $defaultPath;

	/**
	 * All of the named path hints.
	 *
	 * @var	array
	 */
	protected $hints = [];

	/**
	 * A cache of whether namespaces and groups exists.
	 *
	 * @var	array
	 */
	protected $exists = [];

	/**
	 * The cache instance.
	 *
	 * @var	Illuminate\Cache
	 */
	protected $cache;

	/**
	 * Create a new file configuration loader.
	 *
	 * @param	Filesystem	$files
	 * @param	Cache		$cache
	 * @param	string		$defaultPath	The paths to use for loading config files
	 * @return	void
	 */
	public function __construct(Filesystem $files, Cache $cache, $defaultPath)
	{
		$this->files = $files;
		$this->cache = $cache;
		$this->defaultPath = $defaultPath;

		// Set the path to the app config directory
		$this->defaultPath['app'] = APPPATH."config";

		// Get the modules out of the cache
		$modules = $this->cache->get('nova.modules');

		if (is_array($modules))
		{
			foreach ($modules as $module)
			{
				// Set the path to the module's config directory
				if ($this->files->isDirectory(APPPATH."src/Modules/{$module}/config"))
					$this->defaultPath[$module] = APPPATH."src/Modules/{$module}/config";
			}
		}

		// Set the path to the core config directory
		$this->defaultPath['core'] = NOVAPATH."config";
	}

	/**
	 * Load the given configuration group.
	 *
	 * @param	string	$environment	The current environment
	 * @param	string  $group			The group to load
	 * @param	string  $namespace		The namespace to load
	 * @return	array
	 */
	public function load($environment, $group, $namespace = null)
	{
		$items = [];

		// First we'll get the root configuration path for the environment which
		// is where all of the configuration files live for that namespace, as 
		// well as any environment folders with their specific configuration items.
		$path = $this->getPath($namespace);

		// If the path is null, just return the empty items array
		if (is_null($path))
			return $items;

		// Make sure the path is an array
		if ( ! is_array($path))
			$path = array($path);

		// Loop through the paths and load the files
		foreach ($path as $location => $p)
		{
			// First we'll get the main configuration file for the groups. Once 
			// we have that we can check for any environment specific files, 
			// which will get merged on top of the main arrays to make the 
			// environments cascade.
			$file = "{$p}/{$group}.php";

			if ($this->files->exists($file))
				$items[$location] = $this->files->getRequire($file);

			// Finally we're ready to check for the environment specific config
			// file which will be merged on top of the main arrays so that they get
			// precedence over them if we are currently in an environments setup.
			$file = "{$p}/{$environment}/{$group}.php";

			if ($this->files->exists($file))
			{
				// Make sure there's something here if we need it
				if ( ! array_key_exists($location, $items))
					$items[$location] = [];

				$items[$location] = array_merge($items[$location], $this->files->getRequire($file));
			}
		}

		// Flip the array of items and recursively merge them
		$items = call_user_func_array('array_replace_recursive', array_reverse($items));

		return $items;
	}

	/**
	 * Determine if the given group exists.
	 *
	 * @param	string	$group		The group to check
	 * @param	string  $namespace	The namespace to check
	 * @return	bool
	 */
	public function exists($group, $namespace = null)
	{
		$key = $group.$namespace;

		// We'll first check to see if we have determined if this namespace and
		// group combination have been checked before. If they have, we will
		// just return the cached result so we don't have to hit the disk.
		if (isset($this->exists[$key]))
			return $this->exists[$key];

		$path = $this->getPath($namespace);

		// To check if a group exists, we will simply get the path based on the
		// namespace, and then check to see if this files exists within that
		// namespace. False is returned if no path exists for a namespace.
		if (is_null($path))
			return $this->exists[$key] = false;

		$file = "{$path}/{$group}.php";

		// Finally, we can simply check if this file exists. We will also cache
		// the value in an array so we don't have to go through this process
		// again on subsequent checks for the existing of the config file.
		$exists = $this->files->exists($file);

		return $this->exists[$key] = $exists;
	}

	/**
	 * Apply any cascades to an array of package options.
	 *
	 * @param	string	$environment	The environment
	 * @param	string	$package		The package
	 * @param	string	$group			The group
	 * @param	array	$items			The items
	 * @return	array
	 */
	public function cascadePackage($environment, $package, $group, $items)
	{
		// First we will look for a configuration file in the packages configuration
		// folder. If it exists, we will load it and merge it with these original
		// options so that we will easily "cascade" a package's configurations.
		$file = "packages/{$package}/{$group}.php";

		foreach ($this->defaultPath as $location => $p)
		{
			if ($this->files->exists($path = $p.'/'.$file))
				$items = array_merge($items, $this->getRequire($path));

			// Once we have merged the regular package configuration we need to look for
			// an environment specific configuration file. If one exists, we will get
			// the contents and merge them on top of this array of options we have.
			$path = $p."/{$environment}/".$file;

			if ($this->files->exists($path))
				$items = array_merge($items, $this->getRequire($path));
		}

		return $items;
	}

	/**
	 * Get the configuration path for a namespace.
	 *
	 * @param	string	$namespace	The namespace to get the path for
	 * @return	string
	 */
	protected function getPath($namespace)
	{
		if (is_null($namespace))
			return $this->defaultPath;
		
		elseif (isset($this->hints[$namespace]))
			return $this->hints[$namespace];
	}

	/**
	 * Add a new namespace to the loader.
	 *
	 * @param	string	$namespace	The namespace to add
	 * @param	string  $hint		The hint
	 * @return	void
	 */
	public function addNamespace($namespace, $hint)
	{
		$this->hints[$namespace] = $hint;
	}

	/**
	 * Get a file's contents by requiring it.
	 *
	 * @param	string	$path	The file to require
	 * @return	mixed
	 */
	protected function getRequire($path)
	{
		return $this->files->getRequire($path);
	}

	/**
	 * Get the Filesystem instance.
	 *
	 * @return	Filesystem
	 */
	public function getFilesystem()
	{
		return $this->files;
	}

	/**
	 * Returns all registered namespaces with the config
	 * loader.
	 *
	 * @return	array
	 */
	public function getNamespaces()
	{
		return $this->hints;
	}

	/**
	 * Returns the paths that will be checked.
	 *
	 * @return	array
	 */
	public function getPaths()
	{
		return $this->defaultPath;
	}

}