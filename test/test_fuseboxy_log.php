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