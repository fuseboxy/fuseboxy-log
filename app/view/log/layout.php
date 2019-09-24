<?php
if ( !empty($_SESSION['logController__filterField']) ) {
	// pill layout : config
	$tabLayout = array(
		'style' => 'pills',
		'position' => 'left',
		'nav' => call_user_func(function(){
			$menus = array();
			// get value for filter
			if ( $_SESSION['logController__filterField'] != 'datetime' ) {
				$items = Log::getDistinct($_SESSION['logController__filterField']);
			}
			foreach ( $items as $item ) {
				$menus[] = array(
					'name' => empty($item) ? '<em>(empty)</em>' : $item,
					'url' => F::url(F::command('controller')."&filter={$_SESSION['logController__filterField']}:{$item}"),
					'active' => ( isset($_SESSION['logController__filterValue']) and $_SESSION['logController__filterValue'] == $item ),
					'class' => 'small',
				);
			}
			// done!
			return $menus;
		}),
	);
	// pill layout : display
	ob_start();
	include F::config('appPath').'view/global/tab.php';
	$layout['content'] = ob_get_clean();
}


// tab layout : config
$tabLayout = array(
	'style' => 'tabs',
	'position' => 'left',
	'header' => '<h3>Log</h3>',
	'nav' => array(
		array('name' => 'All',       'url' => F::url("{$fusebox->controller}&filter"),             'active' => empty($_SESSION['logController__filterField'])),
//		array('name' => 'By Date',   'url' => F::url("{$fusebox->controller}&filter=datetime"),    'active' => ( isset($_SESSION['logController__filterField']) and $_SESSION['logController__filterField'] == 'datetime' )),
		array('name' => 'By User',   'url' => F::url("{$fusebox->controller}&filter=username"),    'active' => ( isset($_SESSION['logController__filterField']) and $_SESSION['logController__filterField'] == 'username' )),
		array('name' => 'By Action', 'url' => F::url("{$fusebox->controller}&filter=action"),      'active' => ( isset($_SESSION['logController__filterField']) and $_SESSION['logController__filterField'] == 'action' )),
		array('name' => 'By Entity', 'url' => F::url("{$fusebox->controller}&filter=entity_type"), 'active' => ( isset($_SESSION['logController__filterField']) and $_SESSION['logController__filterField'] == 'entity_type' )),
	),
);
// tab layout : display
ob_start();
include F::config('appPath').'view/global/tab.php';
$layout['content'] = ob_get_clean();


// global layout
include F::config('appPath').'view/global/layout.php';