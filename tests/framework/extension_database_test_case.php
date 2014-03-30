<?php
/**
*
* @package testing
* @copyright (c) 2014 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

abstract class extension_database_test_case extends phpbb_database_test_case
{
	static protected $already_connected = false;

	public function getConnection()
	{
		global $phpbb_root_path, $phpEx;

		$config = $this->get_database_config();

		$manager = $this->create_connection_manager($config);

		if (!self::$already_connected)
		{
			$manager->recreate_db();
		}

		$manager->connect();

		if (!self::$already_connected)
		{
			// Install phpBB's schema
			$manager->load_schema($this->new_dbal());

			// We must create a config file to be able to create the container
			$this->create_config_file($phpbb_root_path, $phpEx);

			// Setup the container
			require_once($phpbb_root_path . 'includes/functions_container.' . $phpEx);
			$this->container = phpbb_create_default_container($phpbb_root_path, $phpEx);

			// Setup some globals needed to add schema data and module data to the tables
			global $cache, $db, $phpbb_log, $phpbb_container;

			$phpbb_container = $this->container;
			$cache = $phpbb_container->get('cache');
			$db = $phpbb_container->get('dbal.conn');
			$phpbb_log = $phpbb_container->get('log');

			// Tables have been built, let's fill in the basic information
			$this->add_schema_data($db, $phpbb_root_path, $phpEx);

			// Setup and populate the module tables
			define('IN_INSTALL', true);

			// Load module and install classes needed to populate the module tables
			require_once(dirname(__FILE__) . '/extension_module_class.' . $phpEx);
			require_once($phpbb_root_path . 'includes/functions_module.' . $phpEx);
			require_once($phpbb_root_path . 'install/install_install.' . $phpEx);

			// Populate the module tables
			$install = new install_install(new p_master());
			$install->add_modules(null, null);

			$migrations_path = $phpbb_root_path . substr(phpbb_realpath(dirname(__FILE__) . '/../../migrations'), strlen(phpbb_realpath($phpbb_root_path))) . '/';

			// If there are any migrations, load and run them all
			if (file_exists($migrations_path) && is_dir($migrations_path))
			{
				$finder = $this->container->get('ext.finder');
				$migrator = $this->container->get('migrator');

				$migrations = array();
				$vendor_ext = $this->get_vendor_ext($phpbb_root_path);

				$files = $finder
					->extension_directory("/")
					->find_from_paths(array('/' => $migrations_path));
				foreach ($files as $file)
				{
					$file_name = substr($file['named_path'], 0, -(strlen($phpEx) + 1));

					$migrations[] = $vendor_ext['namespace'] . '\migrations\\' . str_replace('/', '\\', $file_name);
				}

				$migrator->set_migrations($migrations);

				foreach ($migrations as $migration)
				{
					if ($migrator->unfulfillable($migration))
					{
						$this->fail('Migration ' . $migration . ' is unfulfillable');
					}
				}

				while (!$migrator->finished())
				{
					$migrator->update();
				}
			}

			self::$already_connected = true;
		}

		return $this->createDefaultDBConnection($manager->get_pdo(), 'testdb');
	}

	protected function add_schema_data($db, $phpbb_root_path, $phpEx)
	{
		require_once($phpbb_root_path . 'includes/functions_install.' . $phpEx);

		$config = phpbb_test_case_helpers::get_test_config();

		// Load the schema_data
		$sql_query = file_get_contents($phpbb_root_path . 'install/schemas/schema_data.sql');

		// Deal with any special comments and characters
		switch ($config['dbms'])
		{
			case 'mssql':
			case 'mssql_odbc':
			case 'mssqlnative':
				$sql_query = preg_replace('#\# MSSQL IDENTITY (phpbb_[a-z_]+) (ON|OFF) \##s', 'SET IDENTITY_INSERT \1 \2;', $sql_query);
			break;

			case 'postgres':
				$sql_query = preg_replace('#\# POSTGRES (BEGIN|COMMIT) \##s', '\1; ', $sql_query);
			break;

			case 'mysql':
			case 'mysqli':
				$sql_query = str_replace('\\', '\\\\', $sql_query);
			break;
		}

		// Change language strings...
		$sql_query = preg_replace_callback('#\{L_([A-Z0-9\-_]*)\}#s', 'adjust_language_keys_callback', $sql_query);

		$sql_query = phpbb_remove_comments($sql_query);
		$sql_query = split_sql_file($sql_query, ';');

		foreach ($sql_query as $sql)
		{
			//$sql = trim(str_replace('|', ';', $sql));
			if (!$db->sql_query($sql))
			{
				$error = $db->sql_error();
				$this->fail($error['message']);
			}
		}
	}

	protected function get_vendor_ext($phpbb_root_path)
	{
		$migrations_path = realpath(dirname(__FILE__) . '/../../migrations');
		$phpbb_ext_path = realpath($phpbb_root_path . 'ext/');
		$migrations_path = trim(substr($migrations_path, strlen($phpbb_ext_path)), DIRECTORY_SEPARATOR);

		$migrations_path = explode(DIRECTORY_SEPARATOR, $migrations_path);

		return array(
			'vendor'	=> $migrations_path[0],
			'ext'		=> $migrations_path[1],
			'namespace'	=> '\\' . $migrations_path[0] . '\\' . $migrations_path[1],
		);
	}

	protected function create_config_file($phpbb_root_path, $phpEx)
	{
		$config = phpbb_test_case_helpers::get_test_config();

		$contents = "<?php
\$dbms = '" . $config['dbms'] . "';
\$dbhost = '" . $config['dbhost'] . "';
\$dbport = '" . $config['dbport'] . "';
\$dbname = '" . $config['dbname'] . "';
\$dbuser = '" . $config['dbuser'] . "';
\$dbpasswd = '" . $config['dbpasswd'] . "';
\$table_prefix = 'phpbb_';
\$adm_relative_path = 'adm/';
\$acm_type = 'null';

@define('PHPBB_INSTALLED', true);
@define('DEBUG', true);

";
		if (file_put_contents($phpbb_root_path . 'config.' . $phpEx, $contents) === false)
		{
			$this->fail('Could not create config file.');
		}
	}
}
