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
		$result = array();
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
			get corresponding entity record
		</structure>
		<io>
			<in>
				<object name="$log">
					<string name="entity_type" />
					<number name="entity_id" />
				</object>
			</in>
			<out>
				<object name="~return~" type="~entityType~" />
			</out>
		</io>
	</fusedoc>
	*/
	public function getEntity($log) {
		// get record (when necessary)
		if ( is_numeric($log) ) {
			$log = ORM::get('log', $log);
			if ( $log === false ) {
				self::$error = 'Error loading log record ('.ORM::error().')';
				return false;
			} elseif ( empty($log->id) ) {
				self::$error = 'Log record not found (id='.$log->id.')';
				return false;
			}
		}
		// when no entity specified
		// ===> simply return nothing
		if ( empty($log->entity_type) or empty($log->entity_id) ) return null;
		// get entity record
		$result = ORM::get($log->entity_type, $log->entity_id);
		if ( $result === false ) {
			self::$error = 'Error loading entity ('.ORM::error().')';
			return false;
		}
		// done!
		return $result;
	}




	/**
	<fusedoc>
		<description>
			parse log remark into array
		</description>
		<io>
			<in>
				<object name="$log">
					<string name="remark" comments="example as followings">
						[field_1] .....
						[field_2] .....
						[field_3] .....
						[field..] .....
					</string>
				</object>
			</in>
			<out>
				<structure name="~return~" optional="yes" oncondition="when {fieldName} not specified">
					<string name="~fieldName~" />
				</structure>
				<string name="~return~" optional="yes" oncondition="when {fieldName} specified" />
			</out>
		</io>
	</fusedoc>
	*/
	public static function parseRemark($log) {
		$result = array();
		// get record (when necessary)
		if ( is_numeric($log) ) {
			$log = ORM::get('log', $log);
			if ( $log === false ) {
				self::$error = 'Error loading log record ('.ORM::error().')';
				return false;
			} elseif ( empty($log->id) ) {
				self::$error = 'Log record not found (id='.$log->id.')';
				return false;
			}
		}
		// validate format
		// ===> see if first line begins with [....] string (field name)
		$remark = trim($log->remark);
		$allRows = array_map('trim', explode("\n", $remark));
		$firstRow = $allRows[0] ?? '';
		$isBeginWithFieldName = ( $firstRow[0] == '[' and strpos($firstRow, ']') > 1 );
		// when format invalid (e.g. normal string)
		// ===> simply return as one item array
		if ( !$isBeginWithFieldName ) return array($remark);
		// go through each row
		// ===> append row to previous item instead
		foreach ( $allRows as $i => $row ) {
			$isBeginWithFieldName = ( $row[0] == '[' and strpos($row, ']') > 1 );
			// when begins with field name
			// ===> start a new item
			if ( $isBeginWithFieldName ) {
				list($fieldName, $rowWithoutFieldName) = explode(']', substr($row, 1), 2);
				$result[$fieldName] = $rowWithoutFieldName;
			// otherwise
			// ===> append to current item
			} else {
				$result[$fieldName] .= "\n".$row;
			}
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
			self::$error = 'Log [action] was not specified';
			return false;
		}
		if ( !is_string($log['action']) ) {
			self::$error = 'Log [action] must be string';
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
		// create new record
		$bean = ORM::saveNew('log', $log);
		if ( $bean === false ) {
			self::$error = 'Error creating log record ('.ORM::error().')';
			return false;
		}
		// done!
		return $bean->id;
	}


} // class