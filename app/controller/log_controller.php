<?php
F::redirect('auth', !Auth::user());
F::error('Forbidden', !Auth::userInRole('SUPER,ADMIN'));


// default filter value
if ( isset($arguments['filterField']) and !isset($arguments['filterValue']) and $arguments['filterField'] != 'remark' ) {
	$arr = Log::getDistinct($arguments['filterField']);
	F::error(Log::error(), $arr === false);
	if ( stripos($arguments['filterField'], 'datetime') !== false ) $arr = array_reverse($arr);
	$arguments['filterValue'] = isset($arr[0]) ? $arr[0] : '';
}


// config
$scaffold = array_merge([
	'beanType' => 'log',
	'allowNew' => false,
	'allowEdit' => false,
	'allowToggle' => false,
	'allowDelete' => Auth::userInRole('SUPER'),
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
		'id' => '60',
		'datetime' => '13%',
		'username|sim_user' => '13%',
		'action|ip' => '13%',
		'entity_type|entity_id' => '13%',
		'remark' => '30%',
	),
	'fieldConfig' => array(
		'id',
		'datetime' => array('label' => 'Date <small class="muted">/ Time</small>'),
		'username',
		'sim_user',
		'action',
		'entity_type' => array('label' => 'Entity'),
		'entity_id' => array('label' => false),
		'remark',
		'ip' => array('label' => 'IP'),
	),
	'scriptPath' => array(
		'row' => F::appPath('view/log/row.php'),
	),
	'pagination' => true,
], $logScaffold ?? $log_controller ?? []);


// component
include F::appPath('controller/scaffold_controller.php');