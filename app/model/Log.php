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
			self::$error = '['.__CLASS__.'::'.__FUNCTION__.'] Illegal character found';
			return false;
		} elseif ( $column == 'remark' ) {
			self::$error = '['.__CLASS__.'::'.__FUNCTION__.'] Refused to get distinct records of remark';
			return false;
		} elseif ( $column == 'datetime' ) {
			self::$error = '['.__CLASS__.'::'.__FUNCTION__.'] Refused to get distinct records of datetime';
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
	public static function getEntity($log) {
		// get record (when necessary)
		if ( is_numeric($log) ) {
			$log = ORM::get('log', $log);
			if ( $log === false ) {
				self::$error = '['.__CLASS__.'::'.__FUNCTION__.'] Error loading log record - '.ORM::error();
				return false;
			} elseif ( empty($log->id) ) {
				self::$error = '['.__CLASS__.'::'.__FUNCTION__.'] Log record not found (id='.$log->id.')';
				return false;
			}
		}
		// when no entity specified
		// ===> simply return nothing
		if ( empty($log->entity_type) or empty($log->entity_id) ) return null;
		// get entity record
		$result = ORM::get($log->entity_type, $log->entity_id);
		if ( $result === false ) {
			self::$error = '['.__CLASS__.'::'.__FUNCTION__.'] Error loading entity - '.ORM::error();
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
						[badge_1] .....
						[badge_2] .....
						[badge_3] .....
						[badge..] .....
					</string>
				</object>
			</in>
			<out>
				<structure name="~return~" optional="yes" oncondition="when {badge} not specified">
					<string name="~badge~" />
				</structure>
				<string name="~return~" optional="yes" oncondition="when {badge} specified" />
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
				self::$error = '['.__CLASS__.'::'.__FUNCTION__.'] Error loading log record - '.ORM::error();
				return false;
			} elseif ( empty($log->id) ) {
				self::$error = '['.__CLASS__.'::'.__FUNCTION__.'] Log record not found (id='.$log->id.')';
				return false;
			}
		}
		// validate format
		// ===> see if any line begins with [....] string (field name)
		$remark = trim($log->remark);
		$allRows = array_map('trim', explode("\n", $remark));
		$isRowBeginWithBadge = array_map(fn($row) => ( strlen($row) and $row[0] == '[' and strpos($row, ']') > 1 ), $allRows);
		$isAnyRowBeingWithBadge = !empty(array_filter($isRowBeginWithBadge));
		// when format invalid (e.g. normal string)
		// ===> simply return as one item array
		if ( !$isAnyRowBeingWithBadge ) return array($remark);
		// go through each row
		// ===> append row to previous item instead
		foreach ( $allRows as $i => $row ) {
			// when begins with field/badge
			// ===> start a new item
			if ( $isRowBeginWithBadge[$i] ) {
				// split row into badge & content
				list($currentBadge, $rowWithoutBadge) = explode(']', substr($row, 1), 2);
				// make badge unique (to avoid same badge name appears in different rows)
				while ( isset($result[$currentBadge]) ) $currentBadge .= ' ';
				// append to result
				$result[$currentBadge] = $rowWithoutBadge;
			// otherwise
			// ===> append to current item
			} elseif ( isset($currentBadge) ) {
				$result[ $currentBadge] .= "\n".$row;
			} else {
				$result[] = $row;
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
			self::$error = '['.__CLASS__.'::'.__FUNCTION__.'] Log [action] was not specified';
			return false;
		}
		if ( !is_string($log['action']) ) {
			self::$error = '['.__CLASS__.'::'.__FUNCTION__.'] Log [action] must be string';
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
			self::$error = '['.__CLASS__.'::'.__FUNCTION__.'] Error creating log record - '.ORM::error();
			return false;
		}
		// done!
		return $bean->id;
	}


} // class