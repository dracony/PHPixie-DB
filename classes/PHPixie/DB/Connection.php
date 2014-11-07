<?php

namespace PHPixie\DB;

/**
 * Database related functions. Creates connections,
 * executes queries and returns results. It is also the
 * generic connection class used by drivers.
 * @package Database
 */
abstract class Connection
{
    /**
     * @var \PHPixie\Pixie
     */
	public $pixie;

    /**
     * @param \PHPixie\Pixie $pixie
     * @param $config
     */
	public function __construct($pixie, $config) {
		$this->pixie = $pixie;
	}
	
	/**
	 * Executes a prepared statement query
	 *
	 * @param string   $query  A prepared statement query
	 * @param array     $params Parameters for the query
	 * @return Result
	 * @see Result_Database
	 */
	public abstract function execute($query, $params = array());

	/**
	 * Builds a new Query to the database
	 *
	 * @param string $type Query type. Available types: select, update, insert, delete, count
	 * @return Query
	 * @see Query
	 */
	public abstract function query($type);

	/**
	 * Gets the id of the last inserted row.
	 *
	 * @return mixed The id of the last inserted row
	 */
	public abstract function insert_id();

	/**
	 * Gets column names for the specified table
	 *
	 * @param string $table Name of the table to get columns from
	 * @return array Array of column names
	 */
	public abstract function list_columns($table);

	/**
	 * Executes a named query where parameters are passed as an associative array
	 * Example:
	 * <code>
	 * $result=$db->namedQuery("SELECT * FROM fairies where name = :name",array('name'=>'Tinkerbell'));
	 * </code>
	 *
	 * @param string $query  A named query
	 * @param array   $params Associative array of parameters
	 * @return Result   Current drivers implementation of Result_Database
	 */
	public function named_query($query, $params = array())
	{
		$bind = array();
		preg_match_all('#:(\w+)#is', $query, $matches, PREG_SET_ORDER);
		foreach ($matches as $match)
		{
			if (isset($params[$match[1]]))
			{
				$query = preg_replace("#{$match[0]}#", '?', $query, 1);
				$bind[] = $params[$match[1]];
			}
		}
		return $this->execute($query, $bind);
	}

	/**
	 * Returns an Expression representation of the value.
	 * Values wrapped inside Expression are not escaped in queries
	 *
	 * @param mixed $value Value to be wrapped
     * @param array $params Escaped parameters
	 * @return \PHPixie\Db\Expression  Raw value that will not be escaped during query building
	 */
	public function expr($value, $params = array())
	{
		return $this->pixie->db->expr($value, $params);
	}

}
