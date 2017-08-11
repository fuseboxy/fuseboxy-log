<?php
class TestFuseboxyLog extends UnitTestCase {


	function __construct() {
		if ( !class_exists('Framework') ) {
			include __DIR__.'/utility-log/framework/1.0.3/fuseboxy.php';
			Framework::$mode = Framework::FUSEBOX_UNIT_TEST;
			Framework::$configPath = __DIR__.'/utility-log/config/fusebox_config.php';
		}
		if ( !class_exists('F') ) {
			include __DIR__.'/utility-log/framework/1.0.3/F.php';
		}
		if ( !class_exists('Log') ) {
			include dirname(__DIR__).'/app/model/Log.php';
		}
		if ( !class_exists('Auth') ) {
			include __DIR__.'/utility-log/model/Auth.php';
		}
		if ( !class_exists('Sim') ) {
			include __DIR__.'/utility-log/model/Sim.php';
		}
		if ( !class_exists('R') ) {
			include __DIR__.'/utility-log/redbeanphp/4.3.3/rb.php';
			include __DIR__.'/utility-log/config/rb_config.php';
		}
	}


	function test__Log__count(){
		// create dummy records
		$data = array(
			array('action' => 'SELECT_RECORD', 'datetime' => '2000-01-01T01:00:00', 'username' => 'unit-test', 'sim_user' => null,      'entity_type' => null,       'entity_id' => null, 'remark' => 'something good',  'ip' => '127.0.0.1'),
			array('action' => 'SELECT_RECORD', 'datetime' => '2000-02-02T02:00:00', 'username' => 'unit-test', 'sim_user' => null,      'entity_type' => null,       'entity_id' => null, 'remark' => 'something good',  'ip' => '127.0.0.1'),
			array('action' => 'SELECT_RECORD', 'datetime' => '2000-03-03T03:00:00', 'username' => 'unit-test', 'sim_user' => null,      'entity_type' => null,       'entity_id' => null, 'remark' => 'something good',  'ip' => '127.0.0.1'),
			array('action' => 'SELECT_RECORD', 'datetime' => '2000-04-04T04:00:00', 'username' => 'unit-test', 'sim_user' => 'foo-bar', 'entity_type' => null,       'entity_id' => null, 'remark' => 'something good',  'ip' => '127.0.0.1'),
			array('action' => 'INSERT_RECORD', 'datetime' => '2000-05-05T05:00:00', 'username' => 'unit-test', 'sim_user' => 'foo-bar', 'entity_type' => 'whatever', 'entity_id' => 1,    'remark' => 'something bad',   'ip' => '127.0.0.1'),
			array('action' => 'INSERT_RECORD', 'datetime' => '2000-06-06T06:00:00', 'username' => 'unit-test', 'sim_user' => 'foo-bar', 'entity_type' => 'whatever', 'entity_id' => 2,    'remark' => 'something bad',   'ip' => '127.0.0.1'),
			array('action' => 'INSERT_RECORD', 'datetime' => '2000-07-07T07:00:00', 'username' => 'foo-bar',   'sim_user' => 'foo-bar', 'entity_type' => 'whatever', 'entity_id' => 3,    'remark' => 'something bad',   'ip' => '127.0.0.1'),
			array('action' => 'UPDATE_RECORD', 'datetime' => '2000-08-08T08:00:00', 'username' => 'foo-bar',   'sim_user' => 'foo-bar', 'entity_type' => 'whatever', 'entity_id' => 4,    'remark' => 'nothing special', 'ip' => '127.0.0.1'),
			array('action' => 'UPDATE_RECORD', 'datetime' => '2000-09-09T09:00:00', 'username' => 'foo-bar',   'sim_user' => 'foo-bar', 'entity_type' => 'whatever', 'entity_id' => 5,    'remark' => 'nothing special', 'ip' => '127.0.0.1'),
			array('action' => 'DELETE_RECORD', 'datetime' => '2000-10-10T10:00:00', 'username' => 'foo-bar',   'sim_user' => 'foo-bar', 'entity_type' => 'whatever', 'entity_id' => 6,    'remark' => 'nothing special', 'ip' => '127.0.0.1'),
		);
		foreach ( $data as $i => $item ) {
			$bean = R::dispense('log');
			$bean->import($item);
			$id = R::store($bean);
			$this->assertTrue( !empty($id) );
		}
		// count all records
		$result = Log::count();
		$this->assertTrue( $result == 10 );
		// count with filter
		$result = Log::count('action = ?', array('SELECT_RECORD'));
		$this->assertTrue( $result == 4 );
		$result = Log::count('datetime BETWEEN ? AND ?', array('2000-01-01T00:00:00', '2000-06-30T23:59:59'));
		$this->assertTrue( $result == 6 );
		$result = Log::count('action IN (?,?) AND username = ?', array('INSERT_RECORD', 'UPDATE_RECORD', 'unit-test'));
		$this->assertTrue( $result == 2);
		// count with filter (no param)
		$result = Log::count(" action = 'SELECT_RECORD' ");
		$this->assertTrue( $result == 4 );
		// no record matched
		$result = Log::count('action = ?', array('LOGIN'));
		$this->assertTrue( $result == 0 );
		// clean-up
		R::nuke();
	}


	function test__Log__find(){
		// create dummy records
		$data = array(
			array('action' => 'SELECT_RECORD', 'datetime' => '2000-01-01T01:00:00', 'username' => 'unit-test', 'sim_user' => null,      'entity_type' => null,       'entity_id' => null, 'remark' => 'something good',  'ip' => '127.0.0.1'),
			array('action' => 'SELECT_RECORD', 'datetime' => '2000-02-02T02:00:00', 'username' => 'unit-test', 'sim_user' => null,      'entity_type' => null,       'entity_id' => null, 'remark' => 'something good',  'ip' => '127.0.0.1'),
			array('action' => 'SELECT_RECORD', 'datetime' => '2000-03-03T03:00:00', 'username' => 'unit-test', 'sim_user' => null,      'entity_type' => null,       'entity_id' => null, 'remark' => 'something good',  'ip' => '127.0.0.1'),
			array('action' => 'SELECT_RECORD', 'datetime' => '2000-04-04T04:00:00', 'username' => 'unit-test', 'sim_user' => 'foo-bar', 'entity_type' => null,       'entity_id' => null, 'remark' => 'something good',  'ip' => '127.0.0.1'),
			array('action' => 'INSERT_RECORD', 'datetime' => '2000-05-05T05:00:00', 'username' => 'unit-test', 'sim_user' => 'foo-bar', 'entity_type' => 'whatever', 'entity_id' => 1,    'remark' => 'something bad',   'ip' => '127.0.0.1'),
			array('action' => 'INSERT_RECORD', 'datetime' => '2000-06-06T06:00:00', 'username' => 'unit-test', 'sim_user' => 'foo-bar', 'entity_type' => 'whatever', 'entity_id' => 2,    'remark' => 'something bad',   'ip' => '127.0.0.1'),
			array('action' => 'INSERT_RECORD', 'datetime' => '2000-07-07T07:00:00', 'username' => 'foo-bar',   'sim_user' => 'foo-bar', 'entity_type' => 'whatever', 'entity_id' => 3,    'remark' => 'something bad',   'ip' => '127.0.0.1'),
			array('action' => 'UPDATE_RECORD', 'datetime' => '2000-08-08T08:00:00', 'username' => 'foo-bar',   'sim_user' => 'foo-bar', 'entity_type' => 'whatever', 'entity_id' => 4,    'remark' => 'nothing special', 'ip' => '127.0.0.1'),
			array('action' => 'UPDATE_RECORD', 'datetime' => '2000-09-09T09:00:00', 'username' => 'foo-bar',   'sim_user' => 'foo-bar', 'entity_type' => 'whatever', 'entity_id' => 5,    'remark' => 'nothing special', 'ip' => '127.0.0.1'),
			array('action' => 'DELETE_RECORD', 'datetime' => '2000-10-10T10:00:00', 'username' => 'foo-bar',   'sim_user' => 'foo-bar', 'entity_type' => 'whatever', 'entity_id' => 6,    'remark' => 'nothing special', 'ip' => '127.0.0.1'),
		);
		foreach ( $data as $i => $item ) {
			$bean = R::dispense('log');
			$bean->import($item);
			$id = R::store($bean);
			$this->assertTrue( !empty($id) );
		}
		// get all records
		$result = Log::find();
		$this->assertTrue( count($result) == 10 );
		// get all records (with order)
		$result = Log::find('ORDER BY action DESC');
		$this->assertTrue( count($result) == 10 );
		$firstBean = array_shift($result);
		$lastBean = array_pop($result);
		$this->assertTrue( $firstBean->action == 'UPDATE_RECORD' and $firstBean->username == 'foo-bar' );
		$this->assertTrue( $lastBean->action  == 'DELETE_RECORD' and $lastBean->username  == 'foo-bar' );
		// get by filter
		$result = Log::find('username = ?', array('unit-test'));
		$this->assertTrue( count($result) == 6 );
		// get by filter (no param)
		$result = Log::find(' username = "foo-bar" ');
		$this->assertTrue( count($result) == 4 );
		// no record matched
		$result = Log::find('action = ?', array('LOGIN'));
		$this->assertTrue( count($result) == 0 );
		// clean-up
		R::nuke();
	}


	function test__Log__findOne(){
		// create dummy records
		$data = array(
			array('action' => 'SELECT_RECORD', 'datetime' => '2000-01-01T01:00:00', 'username' => 'unit-test', 'sim_user' => null,      'entity_type' => null,       'entity_id' => null, 'remark' => 'something good',  'ip' => '127.0.0.1'),
			array('action' => 'SELECT_RECORD', 'datetime' => '2000-02-02T02:00:00', 'username' => 'unit-test', 'sim_user' => null,      'entity_type' => null,       'entity_id' => null, 'remark' => 'something good',  'ip' => '127.0.0.1'),
			array('action' => 'SELECT_RECORD', 'datetime' => '2000-03-03T03:00:00', 'username' => 'unit-test', 'sim_user' => null,      'entity_type' => null,       'entity_id' => null, 'remark' => 'something good',  'ip' => '127.0.0.1'),
			array('action' => 'SELECT_RECORD', 'datetime' => '2000-04-04T04:00:00', 'username' => 'unit-test', 'sim_user' => 'foo-bar', 'entity_type' => null,       'entity_id' => null, 'remark' => 'something good',  'ip' => '127.0.0.1'),
			array('action' => 'INSERT_RECORD', 'datetime' => '2000-05-05T05:00:00', 'username' => 'unit-test', 'sim_user' => 'foo-bar', 'entity_type' => 'whatever', 'entity_id' => 1,    'remark' => 'something bad',   'ip' => '127.0.0.1'),
			array('action' => 'INSERT_RECORD', 'datetime' => '2000-06-06T06:00:00', 'username' => 'unit-test', 'sim_user' => 'foo-bar', 'entity_type' => 'whatever', 'entity_id' => 2,    'remark' => 'something bad',   'ip' => '127.0.0.1'),
			array('action' => 'INSERT_RECORD', 'datetime' => '2000-07-07T07:00:00', 'username' => 'foo-bar',   'sim_user' => 'foo-bar', 'entity_type' => 'whatever', 'entity_id' => 3,    'remark' => 'something bad',   'ip' => '127.0.0.1'),
			array('action' => 'UPDATE_RECORD', 'datetime' => '2000-08-08T08:00:00', 'username' => 'foo-bar',   'sim_user' => 'foo-bar', 'entity_type' => 'whatever', 'entity_id' => 4,    'remark' => 'nothing special', 'ip' => '127.0.0.1'),
			array('action' => 'UPDATE_RECORD', 'datetime' => '2000-09-09T09:00:00', 'username' => 'foo-bar',   'sim_user' => 'foo-bar', 'entity_type' => 'whatever', 'entity_id' => 5,    'remark' => 'nothing special', 'ip' => '127.0.0.1'),
			array('action' => 'DELETE_RECORD', 'datetime' => '2000-10-10T10:00:00', 'username' => 'foo-bar',   'sim_user' => 'foo-bar', 'entity_type' => 'whatever', 'entity_id' => 6,    'remark' => 'nothing special', 'ip' => '127.0.0.1'),
		);
		foreach ( $data as $i => $item ) {
			$bean = R::dispense('log');
			$bean->import($item);
			$id = R::store($bean);
			$this->assertTrue( !empty($id) );
		}
		// get first record
		$result = Log::findOne();
		$this->assertTrue( !empty($result->id) );
		$this->assertTrue( $result->action == 'SELECT_RECORD' and $result->username == 'unit-test' );
		// get first record (with order)
		$result = Log::findOne('ORDER BY action ASC');
		$this->assertTrue( !empty($result->id) );
		$this->assertTrue( $result->action == 'DELETE_RECORD' and $result->username == 'foo-bar' );
		// no record matched
		$result = Log::findOne('action = ?', array('LOGIN'));
		$this->assertTrue( empty($result->id) );
		// clean-up
		R::nuke();
	}


	function test__Log__getDistinct(){
		// create dummy records
		$data = array(
			array('action' => 'SELECT_RECORD', 'datetime' => '2000-01-01T01:00:00', 'username' => 'unit-test', 'sim_user' => null,      'entity_type' => null,       'entity_id' => null, 'remark' => 'something good',  'ip' => '127.0.0.1'),
			array('action' => 'SELECT_RECORD', 'datetime' => '2000-02-02T02:00:00', 'username' => 'unit-test', 'sim_user' => null,      'entity_type' => null,       'entity_id' => null, 'remark' => 'something good',  'ip' => '127.0.0.1'),
			array('action' => 'SELECT_RECORD', 'datetime' => '2000-03-03T03:00:00', 'username' => 'unit-test', 'sim_user' => null,      'entity_type' => null,       'entity_id' => null, 'remark' => 'something good',  'ip' => '127.0.0.1'),
			array('action' => 'SELECT_RECORD', 'datetime' => '2000-04-04T04:00:00', 'username' => 'unit-test', 'sim_user' => 'foo-bar', 'entity_type' => null,       'entity_id' => null, 'remark' => 'something good',  'ip' => '127.0.0.1'),
			array('action' => 'INSERT_RECORD', 'datetime' => '2000-05-05T05:00:00', 'username' => 'unit-test', 'sim_user' => 'foo-bar', 'entity_type' => 'whatever', 'entity_id' => 1,    'remark' => 'something bad',   'ip' => '127.0.0.1'),
			array('action' => 'INSERT_RECORD', 'datetime' => '2000-06-06T06:00:00', 'username' => 'unit-test', 'sim_user' => 'foo-bar', 'entity_type' => 'whatever', 'entity_id' => 2,    'remark' => 'something bad',   'ip' => '127.0.0.1'),
			array('action' => 'INSERT_RECORD', 'datetime' => '2000-07-07T07:00:00', 'username' => 'foo-bar',   'sim_user' => 'foo-bar', 'entity_type' => 'whatever', 'entity_id' => 3,    'remark' => 'something bad',   'ip' => '127.0.0.1'),
			array('action' => 'UPDATE_RECORD', 'datetime' => '2000-08-08T08:00:00', 'username' => 'foo-bar',   'sim_user' => 'foo-bar', 'entity_type' => 'whatever', 'entity_id' => 4,    'remark' => 'nothing special', 'ip' => '127.0.0.1'),
			array('action' => 'UPDATE_RECORD', 'datetime' => '2000-09-09T09:00:00', 'username' => 'foo-bar',   'sim_user' => 'foo-bar', 'entity_type' => 'whatever', 'entity_id' => 5,    'remark' => 'nothing special', 'ip' => '127.0.0.1'),
			array('action' => 'DELETE_RECORD', 'datetime' => '2000-10-10T10:00:00', 'username' => 'foo-bar',   'sim_user' => 'foo-bar', 'entity_type' => 'whatever', 'entity_id' => 6,    'remark' => 'nothing special', 'ip' => '127.0.0.1'),
		);
		foreach ( $data as $i => $item ) {
			$bean = R::dispense('log');
			$bean->import($item);
			$id = R::store($bean);
			$this->assertTrue( !empty($id) );
		}
		// all actions
		$result = Log::getDistinct('action');
		$this->assertTrue( count($result) == 4 );
		$result = implode(',', $result);
		$this->assertFalse( $result == 'SELECT_RECORD,INSERT_RECORD,UPDATE_RECORD,DELETE_RECORD' );
		$this->assertTrue(  $result == 'DELETE_RECORD,INSERT_RECORD,SELECT_RECORD,UPDATE_RECORD' );
		// all entity types (empty is included by default)
		$result = Log::getDistinct('entity_type');
		$this->assertTrue( count($result) == 2 );
		$result = implode(',', $result);
		$this->assertTrue( $result == ',whatever' );
		// get by filter
		$result = Log::getDistinct('username', 'remark LIKE ?', array('%special%'));
		$this->assertTrue( count($result) == 1 );
		$result = implode(',', $result);
		$this->assertTrue( $result == 'foo-bar' );
		// get by filter (no param)
		$result = Log::getDistinct('username', 'sim_user IS NULL');
		$this->assertTrue( count($result) == 1 );
		$result = implode(',', $result);
		$this->assertTrue( $result == 'unit-test' );
		$result = Log::getDistinct('username', 'sim_user IS NOT NULL');
		$this->assertTrue( count($result) == 2 );
		$result = implode(',', $result);
		$this->assertTrue( $result == 'foo-bar,unit-test' );
		// no record matched
		$result = Log::getDistinct('datetime', 'action = ?', array('LOGIN'));
		$this->assertTrue( count($result) == 0 );
		// clean-up
		R::nuke();
	}


	function test__Log__write(){
	}


	function test__Bean__diff(){
	}


	function test__Bean__getColumns(){
	}


	function test__Bean__groupBy(){
	}


	function test__Bean__toString(){
	}


	function test__logController__index(){
	}


	function test__logController__search(){
	}


} // TestFuseboxyLog