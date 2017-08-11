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
		$count = Log::count();
		$this->assertTrue( $count == 10 );
		// count with filter
		$count = Log::count('action = ?', array('SELECT_RECORD'));
		$this->assertTrue( $count == 4 );
		$count = Log::count('datetime BETWEEN ? AND ?', array('2000-01-01T00:00:00', '2000-06-30T23:59:59'));
		$this->assertTrue( $count == 6 );
		$count = Log::count('action IN (?,?) AND username = ?', array('INSERT_RECORD', 'UPDATE_RECORD', 'unit-test'));
		$this->assertTrue( $count == 2);
		// count with filter (no param)
		$count = Log::count(" action = 'SELECT_RECORD' ");
		$this->assertTrue( $count == 4 );
		// no valid record
		$count = Log::count('action = ?', array('LOGIN'));
		$this->assertTrue( $count == 0 );
		// clean-up
		R::nuke();
	}


	function test__Log__find(){
	}


	function test__Log__findOne(){
	}


	function test__Log__getDistinct(){
	}


	function test__Log__write(){
	}


	function test__Log__(){
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