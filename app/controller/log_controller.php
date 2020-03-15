<?php
F::redirect('auth', !Auth::user());
F::redirect(F::config('defaultCommand'), !Auth::activeUserInRole('SUPER,ADMIN'));


// default filter value
if ( isset($arguments['filterField']) and !isset($arguments['filterValue']) and $arguments['filterField'] != 'remark' ) {
	$arr = Log::getDistinct($arguments['filterField']);
	F::error(Log::error(), $arr === false);
	if ( stripos($arguments['filterField'], 'datetime') !== false ) $arr = array_reverse($arr);
	$arguments['filterValue'] = isset($arr[0]) ? $arr[0] : '';
}


// config
$scaffold = array(
	'beanType' => 'log',
	'allowNew' => false,
	'allowEdit' => false,
	'allowToggle' => false,
	'allowDelete' => Auth::activeUserInRole('SUPER'),
	'layoutPath' => F::appPath('view/log/layout.php'),
	'listFilter' => call_user_func(function($arguments){
		if ( isset($arguments['filterField']) and $arguments['filterField'] == 'remark' and !empty($arguments['filterValue']) ) {
			return array(" {$arguments['filterField']} LIKE ? ", array('%'.trim($arguments['filterValue']).'%'));
		} elseif ( isset($arguments['filterField']) and $arguments['filterField'] != 'remark' and isset($arguments['filterValue']) ) {
			return array(" IFNULL({$arguments['filterField']}, '') = ? ", array($arguments['filterValue']));
		} else {
			return false;
		}
	}, $arguments),
	'listOrder' => 'ORDER BY datetime DESC',
	'listField' => array(
		'id' => '5%',
		'datetime' => '13%',
		'username|sim_user' => '13%',
		'action|ip' => '13%',
		'entity_id|entity_type' => '13%',
		'remark' => '30%',
	),
	'fieldConfig' => array(
		'id',
		'datetime' => array('label' => 'Date <small class="muted">/ Time</small>'),
		'username',
		'sim_user',
		'active',
		'entity_id' => array('label' => 'Entity'),
		'entity_type' => array('label' => false),
		'remark',
		'ip' => array('label' => 'IP'),
	),
	'scriptPath' => array(
		'row' => F::appPath('view/log/row.php'),
	),
	'pagination' => true,
);


// component
include F::appPath('controller/scaffold_controller.php');