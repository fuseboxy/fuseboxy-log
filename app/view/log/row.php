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
	// show IP in multi-lines
	$doc->find('div.col-ip')->html(str_replace(',', '<br />', $bean->ip));
	// set word-break of remark
	$doc->find('td.col-remark div.col-remark')->attr('style', 'word-break: break-all;');
	// parse remark (when necessary)
	$remarkRows = explode("\n", $bean->remark);
	foreach ( $remarkRows as $i => $row ) {
		// check if row matches [XXXXX] XXXXXXXXXXX} format
		if ( strpos($row, '[') === 0 and strpos($row, '] ') !== false ) {
			$row = preg_replace(['/\[/', '/]/'], ['<span class="sr-only">[</span><span class="badge badge-light border mr-1">','</span><span class="sr-only">]</span>'], $row, 1);
		}
		// check if having before & after values
		if ( strpos($bean->action, 'UPDATE_') === 0 and strpos($row, ' ===> ') !== false ) {
			$row = preg_replace('/ ===> /', ' <i class="fa fa-arrow-right mx-1"><span class="sr-only">===></span></i> ', $row, 1);
		}
		// append modified row
		$remarkRows[$i] = $row;
	}
	$doc->find('td.col-remark div.col-remark')->html(implode('<br />', $remarkRows));
	// highlight keyword in remark (when necessary)
	if ( isset($arguments['filterField']) and $arguments['filterField'] == 'remark' and !empty($arguments['filterValue']) ) :
		$remark = $doc->find('td.col-remark div.col-remark')->html();
		$remark = preg_replace('/('.$arguments['filterValue'].')/i', '<mark>$1</mark>', $remark);
		$doc->find('td.col-remark div.col-remark')->html($remark);
	endif;
	// put into result
	$output = $doc;
endif;


// display
echo $output;