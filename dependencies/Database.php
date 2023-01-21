<?php

namespace Dependencies;

use mysqli;
use mysqli_result;

/**
 * File storing all functions/classes used with database connection, or for processing results
 *
 * All functions/classes with contact with database or results should be placed in here
 *
 * Links as reference:
 * @link https://www.php.net/manual/en/class.mysqli.php
 * @link https://developer.wordpress.org/coding-standards/inline-documentation-standards/php/
 *
 * @package Database
 * @since 8.0.0
 * @version 1:1.0.0
 * @author Lukas <lkocieln@gmail.com>
 */

	/**
	 * Database use multi-purpose class
	 *
	 * Handles multiple tasks in topic of database connections and processing
	 * Available PUBLIC functions:
	 *  - @see fetch ( Select columns);
	 *  - @see insert ( Insert one row );
	 *  - @see exec ( Execute $sql query and return result )
	 *  - @see exists ( Check if records matching statement exist )
	 *
	 *
	 * Automatically disconnects on __destruct
	*/

	class Database {
		/**
		 * @var string $query Stores last used query
		 * @var string $result Stores last result of executed query
		 * @var string $error Error of mysqli->connect error
		 * @var array $credentials Keeps credentials used to create connection, has default value
		 * @var mysqli $connection Stores connection object
		 */
		public $querry;
		public $result;
		public $error;
		private $credentials = array("servername"=>"localhost", "username"=>"php-msql", "password"=>"php-msql", "dbname"=>"users");
		private $connection;

		/**
		 * Constructor of database object, creates connection with credentials specified, or default
		 *
		 * @param array $credentials Associative array with custom credentials used for connection establishment
		 *
		 * @return Database returns instance of Database object
		*/
		function __construct($credentials = Null) {
			if ($credentials <> Null) { $this->credentials = $credentials; };

			$this->connection = $this->connect();
		  }

		/**
		 * Triggers on destruction of object, closes connection used for db
		 *
		 * @return void This returns void, what it even could return?
		*/
		function __destruct() {
			$this->connection->close();
		}

		/**
		 * Creates connection with database with credentials
		 *
		 * @param mysqli $connection Already created connection that will be used instead of creating brand new
		 *
		 * @return mysqli $connection That was passed in as parameter or just created, returns string with error if error occurred
		*/
		public function connect($connection = Null): mysqli | string | bool {
			if ( $connection <> NULL ) { $this->connection = $connection; return $connection->connect_error; };

			$connection = new mysqli($this->credentials["servername"], $this->credentials["username"], $this->credentials["password"], $this->credentials["dbname"]);

			if ( $connection->connect_errno ) { $this->error = $connection->connect_error; return false; };

			return $connection;
		}

		/**
		 * Converts mysqli result object to associative array(s)
		 *
		 * @param mysqli_result $result Result from query
		 *
		 * @return array $result converted to array with associative ones
		*/
		protected function toAssoc(mysqli_result $result, $purify = true): array {
			$assoc = array();
			if ( $result->num_rows == 1 && $purify == true ) {
				$assoc = $result->fetch_assoc();
			} else {
				foreach( range(1, $result->num_rows) as $i ) {
					$row = $result->fetch_assoc();
					if ( $row == Null && $purify == true ) { continue; }
					if ( count($row) == 1 && $purify == true ) {
						$assoc[] = reset($row);
					} else { $assoc[] = $row; }
				}
			}
			return $assoc;
		}

		/**
		 * Converts associative array to string using multiple separators
		 *
		 * @param string  $splitter String that will separate key - value pairs
		 * @param string $separator String that will separate key and value in each pair
		 * @param array $assoc Associative array that will be converted to string and returned
		 * @param bool $quotes If true all strings in values will be quoted
		 *
		 * @return string Converted $assoc array to string using all other params
		*/
		protected function assocToStr($splitter, $separator, $assoc, $quotes = true): string {
			$str = "";
				foreach( $assoc as $key=>$value) {
					$str .= sprintf(" %s%s%s%s", $key, $separator, is_string($value)?"'{$value}'":$value, array_search($key, array_keys($assoc)) <> count($assoc)-1?$splitter:"" );
				}
			return $str;
		}

		/**
		 * Converts array to string using separator
		 *
		 * @param string $separator String that will separate elements
		 * @param array $assoc Array that will be converted to string and returned
		 * @param bool $quotes If true all strings in values will be quoted
		 *
		 * @return string Converted $array array to string using all other params
		*/
		protected function arrayToStr( $separator, $array, $quotes = true): string {
			$str = "";
			if ( $quotes ) {
				foreach( $array as $element) {
					// $str .= sprintf(" %s%s", is_string($element)?"'{$element}'":$element, array_search($element, $array) <> count($array)-1?$separator:"" );
					$str .= sprintf(" %s%s", is_string($element)?"'{$element}'":$element, $element <> end($array)?$separator:"" );
				}
			} else {
				foreach( $array as $element) {
					// $str .= sprintf(" %s%s", $element, array_search($element, $array) <> count($array)-1?$separator:"" );
					$str .= sprintf(" %s%s", $element, $element <> end($array)?$separator:"" );
				}
			}
			return $str;
		}

		/**
		 * Returns id of last inserted row
		 *
		 * @return int Least inserted id
		 */
		public function last_id(): int {
			return $this->connection->insert_id;
		}

		/**
		 * Check if record with given statements exists in table
		 *
		 * @param string $table Table name that will be used
		 * @param array $statement statements that will be used in WHERE clause
		 *
		 * @return bool Returns true if there is one or more rows, in any other case false
		*/
		public function exists($table, $statement): bool {
			$querry = "SELECT * FROM {$table} WHERE ";
			$querry .= $this->assocToStr(splitter: " AND ", separator: " = ", assoc: $statement, quotes: true);
			$this->querry = $querry;
			// print $querry;
			$this->result = $this->connection->query($querry);
			if( $this->result->num_rows <= 0 ) { return false; }
			else { return true; };
		}

		/**
		 * Executes single query in database
		 *
		 * @param string $query Raw SQL query that will be executed, and result returned
		 *
		 * @return array Returns array with result, false if there was no rows returned
		*/
		public function exec(string $querry): array | bool {
			$this->querry = $querry;
			$this->result = $this->connection->query($querry);

			if ( is_bool( $this->result )) {
				return $this->result;
			}

			if($this->result->num_rows <= 0) { return false; }

			if($this->result->num_rows == 1) {
				return $this->result->fetch_assoc(); }
			else {
				return $this->toAssoc($this->result);
			}
		}

		/**
		 * Executes multiple queries in database
		 *
		 * @param string $query Raw SQL queries that will be executed, and result returned
		 *
		 * @return array Returns array with result, false if there was no rows returned
		*/
		public function execMulti(string $querry ) { // Finish this function
			$this->querry = $querry;
			$this->result = $this->connection->multi_query($querry);
			return $this->result;
			/*
			$resultSets = array();

			do {
				if ($result = $this->connection->store_result()) {
					while ($row = $resultSet->fetch_row()) {
						printf("%s\n", $row[0]);
					}
					$result -> free_result();
				}
				if ($mysqli -> more_results()) {
					printf("-------------\n");
				}
				//Prepare next result set
			} while ($mysqli -> next_result());
			*/
		}

		/**
		 * Selects form database
		 *
		 * @param string $columns String or array with columns that will be fetched, pass `*` for all
		 * @param string $table Name of table that will be fetched from
		 * @param array $statement Used for WHERE clause
		 *
		 *
		 * @return array Returns array with result
		*/
		public function fetch( $columns, $table, array $statement = [], $purify=false): array | bool {
			if ( is_array($columns) ) { $columns = implode( ", ", $columns); };
			$querry = sprintf("SELECT %s FROM %s", $columns, $table);
			if ( count($statement) <> 0 ) {
				$querry .= " WHERE ";
				$querry .= $this->assocToStr(splitter: " AND ", separator: " = ", assoc: $statement, quotes: true);
			}
			$this->querry = $querry;
			$result = $this->connection->query($querry);
			$this->result = $result;
			if ( !$result ) {
				return false;
			}
			return $this->toAssoc($result, $purify);
		}

		/**
		 * Inserts values in to table in db
		 *
		 * @param string $table Name of table that will be used
		 * @param array $values Values to insert
		 *
		 *
		 * @return bool Returns status of insert
		*/
		public function insert( $table, $values ) {
			$querry = sprintf("INSERT INTO %s %s VALUES ", $table, "( ".implode(", ", array_keys($values))." )");
			$querry .= "( ". $this->arrayToStr(separator: ", ", array: array_values($values)) . " )";
			//print $querry;
			$responce = $this->connection->query($querry);
			if ($responce) {
				return true;
			} else {
				return false;
			}
		}

		public function update(string $table, array $values, array $condition = Null): int {
			$values = $this->assocToStr(splitter: ", ", separator: " = ", assoc: $values, quotes: true);
			if ( count($condition) <> 0 ) {
				$conditions = $this->assocToStr(splitter: " AND ", separator: " = ", assoc: $condition, quotes: true);
			}
			$querry = sprintf("UPDATE {$table} SET %s %s", $values, $condition == Null?"":"WHERE {$conditions}");
			$this->connection->query($querry);
			return $this->connection->affected_rows;
		}

		public function delete(string $table, array $condition = Null): int {
			if ( count($condition) <> 0 ) {
				$conditions = $this->assocToStr(splitter: " AND ", separator: " = ", assoc: $condition, quotes: true);
			}
			$querry = sprintf("DELETE FROM {$table} %s", $condition == Null?"":"WHERE {$conditions}");
			$this->connection->query($querry);
			return $this->connection->affected_rows;
		}
	}

	/*
	spl_autoload_register(function ($class_name) {
		include $class_name . '.php';
		});

	*/