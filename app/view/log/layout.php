<?php
if ( !empty($arguments['filterField']) ) {
	// pill layout : config
	$tabLayout = array(
		'style' => 'pills',
		'position' => 'left',
		'nav' => call_user_func(function($arguments){
			$menus = array();
			// get value for filter
			if ( $arguments['filterField'] == 'month' ) {
				$items = array_reverse( Log::getDistinct('DATE_FORMAT(datetime, "%Y-%m")') );
			} else {
				$items = Log::getDistinct($arguments['filterField']);
			}
			// put into menu
			foreach ( $items as $item ) {
				$menus[] = array(
					'name' => empty($item) ? '<em>(empty)</em>' : $item,
					'url' => F::url(F::command('controller')."&filterField={$arguments['filterField']}&filterValue={$item}"),
					'active' => ( isset($arguments['filterValue']) and $arguments['filterValue'] == $item ),
					'class' => 'small',
				);
			}
			// done!
			return $menus;
		}, $arguments),
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
		array('name' => 'All',       'url' => F::url($fusebox->controller),                             'active' => empty($arguments['filterField'])),
//		array('name' => 'By Month',  'url' => F::url("{$fusebox->controller}&filterField=month"),       'active' => ( isset($arguments['filterField']) and $arguments['filterField'] == 'month' )),
		array('name' => 'By User',   'url' => F::url("{$fusebox->controller}&filterField=username"),    'active' => ( isset($arguments['filterField']) and $arguments['filterField'] == 'username' )),
		array('name' => 'By Action', 'url' => F::url("{$fusebox->controller}&filterField=action"),      'active' => ( isset($arguments['filterField']) and $arguments['filterField'] == 'action' )),
		array('name' => 'By Entity', 'url' => F::url("{$fusebox->controller}&filterField=entity_type"), 'active' => ( isset($arguments['filterField']) and $arguments['filterField'] == 'entity_type' )),
//		array('name' => 'By Remark', 'url' => F::url("{$fusebox->controller}&filterField=remark")),
	),
);
// tab layout : display
ob_start();
include F::config('appPath').'view/global/tab.php';
$layout['content'] = ob_get_clean();


// global layout
include F::config('appPath').'view/global/layout.php';