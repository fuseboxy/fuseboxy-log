<?php
if ( !empty($arguments['filterField']) ) {
	// pill layout : config
	$tabLayout = array(
		'style' => 'pills',
		'position' => 'left',
		'headerClass' => false,
		'header' => call_user_func(function($arguments){
			if ( $arguments['filterField'] != 'remark' ) return false;
			// get record count
			if ( !empty(Scaffold::$config['listFilter']) and is_array(Scaffold::$config['listFilter']) ) {
				$totalRecordCount = ORM::count('log', Scaffold::$config['listFilter']['sql'], Scaffold::$config['listFilter']['param']);
			}
			// search form
			ob_start();
			include F::appPath('view/log/search.php');
			return ob_get_clean();
		}, $arguments),
		'nav' => call_user_func(function($arguments){
			if ( $arguments['filterField'] == 'remark' ) {
				return false;
			}
			// get value for filter
			$arr = Log::getDistinct($arguments['filterField']);
			F::error(Log::error(), $arr === false);
			if ( stripos($arguments['filterField'], 'datetime') !== false ) {
				$arr = array_reverse($arr);
			}
			// put into menu
			$menus = array();
			foreach ( $arr as $item ) {
				$menus[] = array(
					'name' => empty($item) ? '<em>(empty)</em>' : $item,
					'url' => F::url(F::command('controller')."&filterField={$arguments['filterField']}&filterValue={$item}"),
					'active' => ( isset($arguments['filterValue']) and $arguments['filterValue'] == $item ),
					'remark' => ORM::count('log', " IFNULL({$arguments['filterField']},'') = ? ", array( empty($item) ? '' : $item )),
					'class' => 'small',
				);
			}
			// done!
			return $menus;
		}, $arguments),
	);
	// pill layout : display
	ob_start();
	include F::appPath('view/tab/layout.php');
	$layout['content'] = ob_get_clean();
}




// tab layout : config
$tabLayout = array(
	'style' => 'tabs',
	'position' => 'left',
	'header' => 'Log',
	'nav' => array(
		array('name' => 'All', 'url' => F::url($fusebox->controller), 'active' => empty($arguments['filterField']), 'remark' => ORM::count('log')),
		array('name' => 'By Month', 'url' => F::url("{$fusebox->controller}&filterField=DATE_FORMAT(datetime, '%Y-%m')"), 'active' => ( isset($arguments['filterField']) and $arguments['filterField'] == "DATE_FORMAT(datetime, '%Y-%m')")),
		array('name' => 'By User', 'url' => F::url("{$fusebox->controller}&filterField=username"), 'active' => ( isset($arguments['filterField']) and $arguments['filterField'] == 'username' )),
		array('name' => 'By Action', 'url' => F::url("{$fusebox->controller}&filterField=action"), 'active' => ( isset($arguments['filterField']) and $arguments['filterField'] == 'action' )),
		array('name' => 'By Entity', 'url' => F::url("{$fusebox->controller}&filterField=entity_type"), 'active' => ( isset($arguments['filterField']) and $arguments['filterField'] == 'entity_type' )),
		array('name' => 'By Remark', 'url' => F::url("{$fusebox->controller}&filterField=remark"), 'active' => ( isset($arguments['filterField']) and $arguments['filterField'] == 'remark' )),
	),
);
// tab layout : display
ob_start();
include F::appPath('view/tab/layout.php');
$layout['content'] = ob_get_clean();




// global layout
$layout['width'] = 'full';
include F::appPath('view/global/layout.php');