<?php /*
<fusedoc>
	<io>
		<in>
			<string name="filterField" scope="$arguments" optional="yes" />
			<string name="filterValue" scope="$arguments" optional="yes" />
			<object name="$bean" type="log">
				<number name="id" />
				<datetime name="datetime" />
				<string name="user" />
				<string name="sim_user" />
				<string name="action" />
				<string name="ip" />
				<string name="entity_type" />
				<string name="entity_id" />
				<string name="remark" />
			</object>
		</in>
		<out />
	</io>
</fusedoc>
*/
// capture original output
ob_start();
include F::appPath('view/scaffold/row.php');
$output = ob_get_clean();


// adjust output (when necessary)
if ( class_exists('Util') ) :
	$doc = Util::phpQuery($output);
	// split date & time
	ob_start();
	?><div class="col-date"><?php echo date('Y-m-d', strtotime($bean->datetime)); ?></div><?php
	?><div class="col-time small text-muted"><?php echo date('H:i:s', strtotime($bean->datetime)); ?></div><?php
	$doc->find('td.col-datetime div.col-datetime')->html(ob_get_clean());
	// word-break remark
	$doc->find('td.col-remark div.col-remark')->attr('style', 'word-break: break-all;');
	// parse remark (when necessary)
	$remarkRows = explode("\n", $bean->remark);
	foreach ( $remarkRows as $i => $row ) {
		// check if row matches [XXXXX] XXXXXXXXXXX} format
		if ( strpos($row, '[') === 0 and strpos($row, '] ') !== false ) {
			$row = preg_replace(['/\[/', '/]/'], ['<span class="badge badge-light border mr-1">','</span>'], $row, 1);
		}
		// check if having before & after values
		if ( strpos($bean->action, 'UPDATE_') === 0 and strpos($row, ' ===> ') !== false ) {
			$row = preg_replace('/ ===> /', '<span class="text-primary mx-1"> ===> </span>', $row, 1);
		}
		// append modified row
		$remarkRows[$i] = $row;
	}
	$doc->find('td.col-remark div.col-remark')->html(implode('<br />', $remarkRows));
	// highlight remark
	if ( isset($arguments['filterField']) and $arguments['filterField'] == 'remark' and !empty($arguments['filterValue']) ) :
		$remark = $doc->find('td.col-remark div.col-remark')->html();
		$startPos = stripos($remark, $arguments['filterValue']);
		$endPos = stripos($remark, $arguments['filterValue']) + strlen($arguments['filterValue']);
		$remark = substr($remark, 0, $endPos).'</mark>'.substr($remark, $endPos);
		$remark = substr($remark, 0, $startPos).'<mark>'.substr($remark, $startPos);
		$doc->find('td.col-remark div.col-remark')->html($remark);
	endif;
	// put into result
	$output = $doc;
endif;


// display
echo $output;