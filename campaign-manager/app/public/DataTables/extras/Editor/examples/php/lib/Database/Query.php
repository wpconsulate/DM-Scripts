<?php
/**
 * DataTables PHP libraries.
 *
 * PHP libraries for DataTables and DataTables Editor, utilising PHP 5.3+.
 *
 *  @author    SpryMedia
 *  @copyright 2012 SpryMedia ( http://sprymedia.co.uk )
 *  @license   http://editor.datatables.net/license DataTables Editor
 *  @link      http://editor.datatables.net
 */

namespace DataTables\Database;
if (!defined('DATATABLES')) exit();

use
	DataTables,
	DataTables\Database,
	DataTables\Database\Query,
	DataTables\Database\Result;


//
// This is a stub class that a driver must extend and complete
//

/**
 * Perform an individual query on the database.
 * 
 * The typical pattern for using this class is through the
 * {@link \DataTables\Database::query} method (and it's 'select', etc short-cuts. 
 * Typically it would not be initialised directly.
 *
 * Note that this is a stub class that a driver will extend and complete as required
 * for individual database types. Individual drives could add additional methods,
 * but this is discouraged to ensure that the API is the same for all database types.
 */
class Query {
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Constructor
	 */

	/**
	 * Query instance constructor.
	 *
	 * Note that typically instances of this class will be automatically created
	 * through the {@link \DataTables\Database::query} method.
	 *  @param Database        $db    Database instance
	 *  @param string          $type  Query type - 'select', 'insert', 'update' or 'delete'
	 *  @param string|string[] $table Tables to operate on - see {@link table}.
	 */
	public function __construct( $db, $type, $table=null )
	{
		$this->_dbcon = $db;
		$this->_type = $type;
		$this->table( $table );
	}


	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Private properties
	 */

	/**
	 * @var string Driver to use
	 * @internal
	 */
	protected $_type = "";

	/**
	 * @var resource Database connection
	 * @internal
	 */
	protected $_dbcon = null;

	/**
	 * @var array
	 * @internal
	 */
	protected $_table = array();

	/**
	 * @var array
	 * @internal
	 */
	protected $_field = array();

	/**
	 * @var array
	 * @internal
	 */
	protected $_value = array();

	/**
	 * @var array
	 * @internal
	 */
	protected $_where = array();

	/**
	 * @var array
	 * @internal
	 */
	protected $_join = array();

	/**
	 * @var array
	 * @internal
	 */
	protected $_order = array();

	/**
	 * @var int
	 * @internal
	 */
	protected $_limit = null;

	/**
	 * @var int
	 * @internal
	 */
	protected $_offset = null;

	/**
	 * @var string
	 * @internal
	 */
	protected $_identifier_limiter = '`';



	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Static methods
	 */

	/**
	 * Commit a transaction.
	 *  @param * $dbh The Database handle (typically a PDO object, but not always).
	 */
	public static function commit ( $dbh )
	{
		$dbh->commit();
	}

	/**
	 * Database connection - override by the database driver.
	 *  @param string $user User name
	 *  @param string $pass Password
	 *  @param string $host Host name
	 *  @param string $db   Database name
	 *  @return Query
	 */
	public static function connect ( $user, $pass, $host, $db )
	{
		return false;
	}


	/**
	 * Start a database transaction
	 *  @param * $dbh The Database handle (typically a PDO object, but not always).
	 */
	public static function transaction ( $dbh )
	{
		$dbh->beginTransaction();
	}


	/**
	 * Rollback the database state to the start of the transaction.
	 *  @param * $dbh The Database handle (typically a PDO object, but not always).
	 */
	public static function rollback ( $dbh )
	{
		$dbh->rollBack();
	}



	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Public properties
	 */

	/**
	 * Execute the query.
	 *  @return Result
	 */
	public function exec ()
	{
		$type = strtolower( $this->_type );

		if ( $type === 'select' ) {
			return $this->_select();
		}
		else if ( $type === 'insert' ) {
			return $this->_insert();
		}
		else if ( $type === 'update' ) {
			return $this->_update();
		}
		else if ( $type === 'delete' ) {
			return $this->_delete();
		}
		
		throw new Exception("Unknown database command or not supported: ".$type, 1);
	}


	/**
	 * Get fields.
	 *  @param string|string[] $get,... Fields to get - can be specified as
	 *    individual fields, an array of fields, a string of comma separated
	 *    fields or any combination of those.
	 *  @return self
	 */
	public function get ( $get )
	{
		if ( $get === null ) {
			return;
		}

		$args = func_get_args();

		for ( $i=0 ; $i<count($args) ; $i++ ) {
			if ( !is_array($args[$i]) ) {
				$get = explode(", ", $args[$i]);
			}

			$this->_field = array_merge( $this->_field, $get );
		}

		return $this;
	}


	/**
	 * Perform a JOIN operation
	 *  @param sting  $table     Table name to do the JOIN on
	 *  @param string $condition JOIN condition
	 *  @param string $type      JOIN type
	 *  @return self
	 */
	public function join ( $table, $condition, $type='' )
	{
		if ($type !== '') {
			$type = strtoupper(trim($type));

			if ( ! in_array($type, array('LEFT', 'RIGHT', 'INNER', 'OUTER', 'LEFT OUTER', 'RIGHT OUTER'))) {
				$type = '';
			}
			else {
				$type .= ' ';
			}
		}

		// Protect the identifiers
		if (preg_match('/([\w\.]+)([\W\s]+)(.+)/', $condition, $match))
		{
			$match[1] = $this->_protect_identifiers( $match[1] );
			$match[3] = $this->_protect_identifiers( $match[3] );

			$condition = $match[1].$match[2].$match[3];
		}

		$this->_join[] = $type .'JOIN '. $this->_protect_identifiers($table) .' ON '. $condition .' ';

		return $this;
	}


	/**
	 * Limit the result set to a certain size.
	 *  @param int $lim The number of records to limit the result to.
	 *  @return self
	 */
	public function limit ( $lim )
	{
		$this->_limit = $lim;

		return $this;
	}


	/**
	 * Set table(s) to perform the query on.
	 *  @param string|string[] $table,... Table(s) to use - can be specified as
	 *    individual names, an array of names, a string of comma separated
	 *    names or any combination of those.
	 *  @return self
	 */
	public function table ( $table )
	{
		if ( $table === null ) {
			return;
		}

		if ( !is_array($table) ) {
			$table = explode(", ", $table);
		}

		for ( $i=0 ; $i<count($table) ; $i++ ) {
			$this->_table[] = $this->_protect_identifiers( $table[$i] );
		}

		return $this;
	}


	/**
	 * Offset the return set by a given number of records (useful for paging).
	 *  @param int $off The number of records to offset the result by.
	 *  @return self
	 */
	public function offset ( $off )
	{
		$this->_offset = $off;

		return $this;
	}


	/**
	 * Order by
	 *  @param string|string[] $table,... Columns and direction to order by - can
	 *    be specified as individual names, an array of names, a string of comma 
	 *    separated names or any combination of those.
	 *  @return self
	 */
	public function order ( $order )
	{
		if ( $order === null ) {
			return;
		}

		if ( !is_array($order) ) {
			$order = explode(", ", $order);
		}

		for ( $i=0 ; $i<count($order) ; $i++ ) {
			// Simplify the white-space
			$order[$i] = preg_replace('/[\t ]+/', ' ', $order[$i]);

			// Find if our identifier has an alias, so we don't escape that
			if ( strpos($order[$i], ' ') !== false ) {
				$direction = strstr($order[$i], ' ');
				$identifier = substr($order[$i], 0, - strlen($alias));
			}
			else {
				$direction = '';
			}

			$this->_order[] = $this->_protect_identifiers( $identifier ).' '.$direction;
		}

		return $this;
	}


	/**
	 * Set fields to a given value.
	 *
	 * Can be used in two different ways, as set( field, value ) or as an array of
	 * fields to set: set( array( 'fieldName' => 'value', ...) );
	 *  @param string|string[] $set Can be given as a single string, when then $val
	 *    must be set, or as an array of key/value pairs to be set.
	 *  @param string          $val When $set is given as a simple string, $set is the field
	 *    name and this is the field's value.
	 *  @return self
	 */
	public function set ( $set, $val=null )
	{
		if ( $set === null ) {
			return;
		}

		if ( !is_array($set) ) {
			$set = array( $set => $val );
		}

		foreach ($set as $key => $value) {
			$this->_field[] = $key;
			$this->_value[] = $value;
		}

		return $this;
	}


	/**
	 * Where query - multiple conditions are bound as ANDs.
	 *
	 * Can be used in two different ways, as where( field, value ) or as an array of
	 * conditions to use: where( array('fieldName', ...), array('value', ...) );
	 *  @param string|string[] $key   Single field name, or an array of field names.
	 *  @param string|string[] $value Single field value, or an array of values.
	 *  @param string          $op    Condition operator: <, >, = etc
	 *  @param boolean         $bind  Escape the value (true, default) or not (false).
	 *  @return self
	 */
	public function where ( $key, $value=null, $op="=", $bind=true )
	{
		if ( !is_array($key) && is_array($value) ) {
			for ( $i=0 ; $i<count($value) ; $i++ ) {
				$this->where( $key, $value[$i], $op, $bind );
			}
			return $this;
		}

		$this->_where( $key, $value, 'AND ', $op, $bind );
		return $this;
	}


	/**
	 * Add addition where conditions to the query with an AND operator.
	 *
	 * Can be used in two different ways, as where( field, value ) or as an array of
	 * conditions to use: where( array('fieldName', ...), array('value', ...) );
	 *  @param string|string[] $key   Single field name, or an array of field names.
	 *  @param string|string[] $value Single field value, or an array of values.
	 *  @param string          $op    Condition operator: <, >, = etc
	 *  @param boolean         $bind  Escape the value (true, default) or not (false).
	 *  @return self
	 */
	public function and_where ( $key, $value=null, $op="=", $bind=true )
	{
		if ( !is_array($key) && is_array($value) ) {
			for ( $i=0 ; $i<count($value) ; $i++ ) {
				$this->and_where( $key, $value[$i], $op, $bind );
			}
			return $this;
		}

		$this->_where( $key, $value, 'AND ', $op, $bind );
		return $this;
	}


	/**
	 * Add addition where conditions to the query with an OR operator.
	 *
	 * Can be used in two different ways, as where( field, value ) or as an array of
	 * conditions to use: where( array('fieldName', ...), array('value', ...) );
	 *  @param string|string[] $key   Single field name, or an array of field names.
	 *  @param string|string[] $value Single field value, or an array of values.
	 *  @param string          $op    Condition operator: <, >, = etc
	 *  @param boolean         $bind  Escape the value (true, default) or not (false).
	 *  @return self
	 */
	public function or_where ( $key, $value=null, $op="=", $bind=true )
	{
		if ( !is_array($key) && is_array($value) ) {
			for ( $i=0 ; $i<count($value) ; $i++ ) {
				$this->or_where( $key, $value[$i], $op, $bind );
			}
			return $this;
		}

		$this->_where( $key, $value, 'OR ', $op, $bind );
		return $this;
	}



	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Private properties
	 */

	/*
	 * SQL string builders
	 */

	/**
	 * Create a SELECT statement
	 *  @return Result
	 *  @internal
	 */
	protected function _select()
	{
		$this->_prepare( 
			'SELECT '.$this->_build_field()
			.'FROM '.$this->_build_table()
			.$this->_build_join()
			.$this->_build_where()
			.$this->_build_order()
			.$this->_build_limit()
		);

		return $this->_exec();
	}


	/**
	 * Create an INSERT statement
	 *  @return Result
	 *  @internal
	 */
	protected function _insert()
	{
		$this->_prepare( 
			'INSERT INTO '
				.$this->_build_table().' ('
					.$this->_build_field()
				.') '
			.'VALUES ('
				.$this->_build_value()
			.')'
		);

		return $this->_exec();
	}


	/**
	 * Create an UPDATE statement
	 *  @return Result
	 *  @internal
	 */
	protected function _update()
	{
		$this->_prepare( 
			'UPDATE '
			.$this->_build_table()
			.'SET '.$this->_build_set()
			.$this->_build_where()
		);

		return $this->_exec();
	}


	/**
	 * Create a DELETE statement
	 *  @return Result
	 *  @internal
	 */
	protected function _delete()
	{
		$this->_prepare( 
			'DELETE FROM '
			.$this->_build_table()
			.$this->_build_where()
		);

		return $this->_exec();
	}


	/*
	 * SQL component specific string builders
	 */

	/**
	 * Create the TABLE list
	 *  @return string
	 *  @internal
	 */
	protected function _build_table()
	{
		return ' '.implode(', ', $this->_table).' ';
	}


	/**
	 * Create a comma separated field list
	 *  @return string
	 *  @internal
	 */
	protected function _build_field()
	{
		$a = array();

		for ( $i=0 ; $i<count($this->_field) ; $i++ ) {
			$a[] = $this->_protect_identifiers( $this->_field[$i] );
		}

		return ' '.implode(', ', $a).' ';
	}


	/**
	 * Create the ORDER BY string
	 *  @return string
	 *  @internal
	 */
	protected function _build_order()
	{
		return ' '.implode(', ', $this->_order).' ';
	}


	/**
	 * Create a bind field value list
	 *  @return string
	 *  @internal
	 */
	protected function _build_value()
	{
		return ' :'.implode(', :', $this->_field).' ';
	}


	/**
	 * Create a JOIN statement list
	 *  @return string
	 *  @internal
	 */
	protected function _build_join()
	{
		return implode(' ', $this->_join);
	}


	/**
	 * Create a set list
	 *  @return string
	 *  @internal
	 */
	protected function _build_set()
	{
		$a = array();

		for ( $i=0 ; $i<count($this->_field) ; $i++ ) {
			$a[] = $this->_protect_identifiers( $this->_field[$i] ) .' = :'. $this->_field[$i];
		}

		return ' '.implode(', ', $a).' ';
	}


	/**
	 * Create the WHERE statement
	 *  @return string
	 *  @internal
	 */
	protected function _build_where()
	{
		if ( count($this->_where) === 0 ) {
			return "";
		}

		$where = "WHERE ";

		for ( $i=0 ; $i<count($this->_where) ; $i++ ) {
			if ( $i !== 0 ) {
				$where .= $this->_where[$i]['operator'];
			}
			$where .= $this->_where[$i]['query'] .' ';
		}

		return $where;
	}


	/**
	 * Create the LIMIT / OFFSET string
	 *
	 * MySQL and Postgres stylee - anything else can have the driver override
	 *  @return string
	 *  @internal
	 */
	protected function _build_limit()
	{
		$out = '';
		
		if ( $this->_limit ) {
			$out .= ' LIMIT '.$this->_limit;
		}

		if ( $this->_offset ) {
			$out .= ' OFFSET '.$this->_offset;
		}

		return $out;
	}


	/**
	 * Add an individual where condition to the query.
	 *  @internal
	 */
	protected function _where ( $where, $value=null, $type='AND ', $op="=", $bind=true )
	{
		$idl = $this->_identifier_limiter;

		if ( $where === null ) {
			return;
		}
		else if ( !is_array($where) ) {
			$where = array( $where => $value );
		}

		foreach ($where as $key => $value) {
			$i = count( $this->_where );

			if ( $bind ) {
				$this->_where[] = array(
					'operator' => $type,
					'field'    => $this->_protect_identifiers($key),
					'value'    => $value,
					'binder'   => ':where_'. str_replace('.', '_', $i),
					'query'    => $this->_protect_identifiers($key) .' '.$op.' :where_'. str_replace('.', '_', $i)
				);
			}
			else {
				$this->_where[] = array(
					'operator' => $type,
					'field'    => null,
					'value'    => null,
					'binder'   => null,
					'query'    => $this->_protect_identifiers($key) .' '. $op .' '. $this->_protect_identifiers($value)
				);
			}
		}
	}


	/**
	 * Protect field names
	 *  @return string
	 *  @internal
	 */
	protected function _protect_identifiers( $identifier )
	{
		$idl = $this->_identifier_limiter;

		// No escaping character
		if ( $idl === '' ) {
			return $identifier;
		}

		// Dealing with a function? Just return immediately
		// This is good enough for most cases, but not all (todo)
		if (strpos($identifier, '(') !== FALSE || strpos($identifier, '*') !== FALSE)
		{
			return $identifier;
		}

		// Going to be operating on the spaces in strings, to simplify the white-space
		$identifier = preg_replace('/[\t ]+/', ' ', $identifier);

		// Find if our identifier has an alias, so we don't escape that
		if ( strpos($identifier, ' ') !== false ) {
			$alias = strstr($identifier, ' ');
			$identifier = substr($identifier, 0, - strlen($alias));
		}
		else {
			$alias = '';
		}

		$a = explode('.', $identifier);
		return $idl . implode($idl.'.'.$idl, $a) . $idl . $alias;
	}
};


