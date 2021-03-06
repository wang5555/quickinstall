<?php
/**
*
* @package quickinstall
* @version $Id$
* @copyright (c) 2007, 2008 eviL3
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
if (!defined('IN_QUICKINSTALL'))
{
	exit;
}

/**
* SQLite dbal extension
* @package dbal
*/
class dbal_sqlite_qi extends dbal_sqlite
{
	/**
	 * Connection error
	 *
	 * @var string
	 */
	var $error = '';

	/**
	 * Used for $this->server
	 */
	var $port;

	/**
	* Connect to server
	*/
	function sql_connect($sqlserver, $sqluser, $sqlpassword, $database, $port = false, $persistency = false , $new_link = false)
	{
		$this->persistency = $persistency;
		$this->user = $sqluser;
		$this->server = $sqlserver . (($port) ? ':' . $port : '');
		$this->dbname = $database;

		$this->port = $port;

		// connect to db
		$this->sql_select_db($this->server);

		return ($this->db_connect_id) ? true : array('message' => $error);
	}

	/**
	 * Select a database
	 *
	 * @param string $dbname
	 */
	function sql_select_db($dbname)
	{
		if (!file_exists($dbname))
		{
			if (empty($dbname))
			{
				global $quickinstall_path;

				$dbname = $quickinstall_path . 'cache/sqlite_db';
			}

			// if file doesn't exist, attempt to create it.
			if (!is_writable(dirname($dbname)))
			{
				trigger_error('SQLite: unable to write to dir ' . dirname($dbname), E_USER_ERROR);
			}

			$fp = @fopen($dbname, 'a');
			@fclose($fp);
			@chmod($dbname, 0777);
		}

		$this->server = $dbname . (($this->port) ? ':' . $this->port : '');

		$this->db_connect_id = ($this->persistency) ? @sqlite_popen($this->server, 0666, $this->error) : @sqlite_open($this->server, 0666, $this->error);

		if ($this->db_connect_id)
		{
			@sqlite_query('PRAGMA short_column_names = 1', $this->db_connect_id);
		}

		return $this->db_connect_id;
	}
}

?>