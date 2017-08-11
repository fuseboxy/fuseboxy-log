<?php
class Log {


	// get latest error message
	private static $error;
	public static function error() { return self::$error; }




	/**
	<fusedoc>
		<description>
			get number of records according to filter and param (if any)
		</description>
		<io>
			<in>
				<string name="$filter" optional="yes" default="1 = 1" />
				<array  name="$param"  optional="yes" />
			</in>
			<out>
				<number name="~return~" />
			</out>
		</io>
	</fusedoc>
	*/
	public static function count($filter='1 = 1', $param=array()) {
		return R::count('log', $filter, $param);
	}




	/**
	<fusedoc>
		<description>
			get all (or specific amount of) records according to filter and param (if any)
		</description>
		<io>
			<in>
				<string name="$filter" optional="yes" default="1 = 1" />
				<array  name="$param"  optional="yes" />
				<number name="$limit"  optional="yes" />
			</in>
			<out>
				<structure name="~return~">
					<object name="~id~" />
				</structure>
			</out>
		</io>
	</fusedoc>
	*/
	public static function find($filter='1 = 1', $param=array(), $limit='') {
		if ( !empty($limit) ) $filter .= " LIMIT {$limit} ";
		return R::find('log', $filter, $param);
	}




	/**
	<fusedoc>
		<description>
			get first record according to filter and param (if any)
		</description>
		<io>
			<in>
				<string name="$filter" optional="yes" default="1 = 1" />
				<array  name="$param"  optional="yes" />
			</in>
			<out>
				<object name="~return~" />
			</out>
		</io>
	</fusedoc>
	*/
	public static function findOne($filter='1 = 1', $param=array()) {
		return R::findOne('log', $filter, $param);
	}




	/**
	<fusedoc>
		<description>
			get deduped values of specific column according to filter (if any)
		</description>
		<io>
			<in>
				<string name="$filter" optional="yes" />
				<array  name="$param"  optional="yes" />
			</in>
			<out>
				<object name="~return~" />
			</out>
		</io>
	</fusedoc>
	*/
	public static function getDistinct($column, $filter='') {
		$sql = "SELECT DISTINCT {$column} FROM log ";
		if ( !empty($filter) ) $sql .= "WHERE {$filter} ";
		$sql .= "ORDER BY {$column} ";
		return R::getAll($sql);
	}




	/**
	<fusedoc>
		<description>
			write log to database
		</description>
		<io>
			<in>
				<string name="$log" optional="yes" comments="simply define action" />
				<structure name="$log" optional="yes" comments="define more details">
					<string name="action" />
					<datetime name="datetime" />
					<string name="username" />
					<string name="sim_user" />
					<string name="entity_type" />
					<number name="entity_id" />
					<string name="remark" />
					<string name="ip" />
				</structure>
			</in>
			<out>
				<number name="~lastInsertID~" />
			</out>
		</io>
	</fusedoc>
	*/
	public static function write($log) {
		$log = is_array($log) ? $log : array('action' => $log);
		// validation
		if ( empty($log['action']) ) {
			self::$error = "Log [action] was not specified";
			return false;
		}
		if ( !is_string($log['action']) ) {
			self::$error = "Log [action] must be string";
			return false;
		}
		// modify data
		$log['action'] = strtoupper($log['action']);
		// default value
		if ( !isset($log['datetime']) ) {
			$log['datetime'] = date('Y-m-d H:i:s');
		}
		if ( !isset($log['username']) and method_exists('Auth', 'user') and Auth::user() ) {
			$log['username'] = Auth::user('username');
		}
		if ( !isset($log['sim_user']) and method_exists('Sim', 'user') and Sim::user() ) {
			$log['sim_user'] = Sim::user('username');
		}
		if ( !isset($log['ip']) and !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
			$log['ip'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif ( !isset($log['ip']) and isset($_SERVER['REMOTE_ADDR']) ) {
			$log['ip'] = $_SERVER['REMOTE_ADDR'];
		}
		// save to database
		$bean = R::dispense('log');
		$bean->import($log);
		$id = R::store($bean);
		// check result
		if ( empty($id) ) {
			self::$error = 'Error occurred while writing log';
			return false;
		}
		// done!
		return $id;
	}


} // Log