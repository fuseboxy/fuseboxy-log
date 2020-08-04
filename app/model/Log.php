<?php
class Log {


	// get latest error message
	private static $error;
	public static function error() { return self::$error; }




	/**
	<fusedoc>
		<description>
			get deduped values of specific column according to filter (if any)
		</description>
		<io>
			<in>
				<string name="$column" comments="reject remark column; handle datetime specially; handle all others normally" />
				<string name="$filter" optional="yes" />
				<array  name="$param"  optional="yes" />
			</in>
			<out>
				<array name="~return~">
					<string name="+" />
				</array>
			</out>
		</io>
	</fusedoc>
	*/
	public static function getDistinct($column, $filter='1=1', $param=array()) {
		// validation
		if ( stripos($column, ';') !== false ) {
			self::$error = 'Illegal character found';
			return false;
		} elseif ( $column == 'remark' ) {
			self::$error = 'Refused to get distinct records of remark';
			return false;
		} elseif ( $column == 'datetime' ) {
			self::$error = 'Refused to get distinct records of datetime';
		}
		// get data
		$sql = "SELECT DISTINCT {$column} FROM log WHERE {$filter} ORDER BY {$column} ASC";
		$data = ORM::query($sql, $param);
		if ( $data === false ) {
			self::$error = ORM::error();
			return false;
		}
		// append to result
		foreach ( $data as $i => $row ) {
			$vals = array_values($row);
			$result[] = !empty($vals) ? $vals[0] : '';
		}
		// done!
		return $result;
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
					<array_or_string name="remark" />
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
		if ( !isset($log['username']) and method_exists('Auth', 'user') and Auth::actualUser() ) {
			$log['username'] = Auth::actualUser('username');
		}
		if ( !isset($log['sim_user']) and method_exists('Sim', 'user') and Sim::user() ) {
			$log['sim_user'] = Sim::user('username');
		}
		if ( !isset($log['ip']) and !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
			$log['ip'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif ( !isset($log['ip']) and isset($_SERVER['REMOTE_ADDR']) ) {
			$log['ip'] = $_SERVER['REMOTE_ADDR'];
		}
		// convert remark to string (when necessary)
		if ( isset($log['remark']) and is_array($log['remark']) ) {
			$arr = array();
			foreach ( $log['remark'] as $key => $val ) {
				$arr[] = !is_numeric($key) ? "[{$key}] {$val}" : $val; 
			}
			$log['remark'] = implode("\n", $arr);
		}
		// create container
		$bean = ORM::new('log', $log);
		if ( $bean === false ) {
			self::$error = ORM::error();
			return false;
		}
		// save to database
		$id = ORM::save($bean);
		if ( $id === false ) {
			self::$error = ORM::error();
			return false;
		}
		// done!
		return $id;
	}


} // class