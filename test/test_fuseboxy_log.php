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
		if ( !class_exists('Bean') ) {
			include dirname(__DIR__).'/app/model/Bean.php';
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
		// create dummy records
		$data = array(
			array('action' => 'SELECT_RECORD', 'datetime' => '2000-04-04T04:00:00', 'username' => 'unit-test', 'sim_user' => 'foo-bar', 'entity_type' => null,       'entity_id' => null, 'remark' => 'something good', 'ip' => '127.0.0.1'),
			array('action' => 'INSERT_RECORD', 'datetime' => '2000-05-05T05:00:00', 'username' => 'unit-test', 'sim_user' => 'foo-bar', 'entity_type' => 'whatever', 'entity_id' => 1,    'remark' => 'something bad',  'ip' => '127.0.0.1'),
		);
		foreach ( $data as $i => $item ) {
			$bean = R::dispense('log');
			$bean->import($item);
			$id = R::store($bean);
			$this->assertTrue( !empty($id) );
		}
		// compare beans
		$beanBefore = R::findOne('log', 'action = ?', array('SELECT_RECORD'));
		$beanAfter  = R::findOne('log', 'action = ?', array('INSERT_RECORD'));
		$result = Bean::diff($beanBefore, $beanAfter);
		// check changed fields
		$this->assertPattern('/\[action\]/i', $result);
		$this->assertPattern('/\[datetime\]/i', $result);
		$this->assertPattern('/\[entity_type\]/i', $result);
		$this->assertPattern('/\[entity_id\]/i', $result);
		$this->assertPattern('/\[remark\]/i', $result);
		// check no-change fields
		$this->assertNoPattern('/\[username\]/i', $result);
		$this->assertNoPattern('/\[sim_user\]/i', $result);
		$this->assertNoPattern('/\[ip\]/i', $result);
		// check detail changes
		$this->assertPattern('/SELECT_RECORD ===> INSERT_RECORD/i', $result);
		$this->assertPattern('/2000-04-04T04:00:00 ===> 2000-05-05T05:00:00/i', $result);
		$this->assertPattern('/\(empty\) ===> whatever/i', $result);
		$this->assertPattern('/\(empty\) ===> 1/i', $result);
		$this->assertPattern('/something good ===> something bad/i', $result);
		// clean-up
		R::nuke();
	}


	function test__Bean__getColumns(){
		// create dummy record
		$bean = R::dispense('log');
		$bean->import(array(
			'action'      => 'SELECT_RECORD',
			'datetime'    => '2000-01-01T01:00:00',
			'username'    => 'unit-test',
			'sim_user'    => null,
			'entity_type' => null,
			'entity_id'   => null,
			'remark'      => 'something good',
			'ip'          => '127.0.0.1',
		));
		$id = R::store($bean);
		$this->assertTrue( !empty($id) );
		// get columns of record
		$bean = R::load('log', $id);
		$result = Bean::getColumns($bean);
		// check columns
		$this->assertTrue( !empty($result) );
		$this->assertTrue( array_search('action',      $result) !== false );
		$this->assertTrue( array_search('datetime',    $result) !== false );
		$this->assertTrue( array_search('username',    $result) !== false );
		$this->assertTrue( array_search('sim_user',    $result) !== false );
		$this->assertTrue( array_search('entity_type', $result) !== false );
		$this->assertTrue( array_search('entity_id',   $result) !== false );
		$this->assertTrue( array_search('remark',      $result) !== false );
		$this->assertTrue( array_search('ip',          $result) !== false );
		// clean-up
		R::nuke();
	}


	function test__Bean__groupBy(){
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
		$all = R::findAll('log');
		// group by action
		$arr = Bean::groupBy('action', $all);
		$result = implode(',', array_keys($arr));
		$this->assertTrue( $result == 'SELECT_RECORD,INSERT_RECORD,UPDATE_RECORD,DELETE_RECORD' );
		// group by username
		$arr = Bean::groupBy('username', $all);
		$result = implode(',', array_keys($arr));
		$this->assertTrue( $result == 'unit-test,foo-bar' );
		// group by column with empty value
		$arr = Bean::groupBy('sim_user', $all);
		$result = implode(',', array_keys($arr));
		$this->assertTrue( $result == ',foo-bar' );
		// group by invalid column
		$arr = Bean::groupBy('noSuchColumn', $all);
		$result = implode(',', array_keys($arr));
		$this->assertTrue( !empty($arr) );
		$this->assertTrue( $result == '' );
		// clean-up
		R::nuke();
	}


	function test__Bean__toString(){
		// create dummy record
		$bean = R::dispense('log');
		$bean->import(array(
			'action'      => 'DELETE_RECORD',
			'datetime'    => '2000-10-10T10:00:00',
			'username'    => 'foo-bar',
			'sim_user'    => null,
			'entity_type' => 'whatever',
			'entity_id'   => 999,
			'remark'      => 'nothing special',
			'ip'          => '127.0.0.1',
		));
		$id = R::store($bean);
		$this->assertTrue( !empty($id) );
		// convert record to string
		$bean = R::load('log', $id);
		$result = Bean::toString($bean);
		// check fields
		$this->assertTrue( !empty($result) );
		$this->assertPattern('/\[action\]/i', $result);
		$this->assertPattern('/\[datetime\]/i', $result);
		$this->assertPattern('/\[username\]/i', $result);
		$this->assertPattern('/\[sim_user\]/i', $result);
		$this->assertPattern('/\[entity_type\]/i', $result);
		$this->assertPattern('/\[entity_id\]/i', $result);
		$this->assertPattern('/\[remark\]/i', $result);
		$this->assertPattern('/\[ip\]/i', $result);
		// check values
		$this->assertPattern('/DELETE_RECORD/i', $result);
		$this->assertPattern('/2000-10-10T10:00:00/i', $result);
		$this->assertPattern('/foo-bar/i', $result);
		$this->assertPattern('/\(empty\)/i', $result);
		$this->assertPattern('/whatever/i', $result);
		$this->assertPattern('/999/i', $result);
		$this->assertPattern('/nothing special/i', $result);
		$this->assertPattern('/127.0.0.1/i', $result);
		// clean-up
		R::nuke();
	}


	function test__logController__index(){
	}


	function test__logController__search(){
	}


} // TestFuseboxyLog