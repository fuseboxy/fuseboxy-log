<?php
F::redirect('auth', !Auth::user());
F::redirect(F::config('defaultCommand'), !Auth::activeUserInRole('SUPER,ADMIN'));


// change selected filter
if ( isset($arguments['filter']) ) {
	$arr = explode(':', $arguments['filter'], 2);
	$arr = array_map('trim', $arr);
	$_SESSION['logController__filterField'] = !empty($arr[0]) ? $arr[0] : '';
	$_SESSION['logController__filterValue'] = !empty($arr[1]) ? $arr[1] : '';
}


// config
$scaffold = array(
	'beanType' => 'log',
	'allowNew' => false,
	'allowEdit' => false,
	'allowToggle' => false,
	'allowDelete' => Auth::activeUserInRole('SUPER'),
	'layoutPath' => F::config('appPath').'view/log/layout.php',
//	'listFilter' => array('type = ?', array($_SESSION['logController__filterBy'])),

	'listOrder' => 'ORDER BY datetime DESC',
	'listField' => array(
		'id' => '7%',
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
		'row' => F::config('appPath').'view/log/row.php',
	),
	'pagination' => true,
);


// component
$layout['width'] = 'full';
include 'scaffold_controller.php';