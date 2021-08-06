<?php /*
<fusedoc>
	<io>
		<in>
			<string name="filterField" scope="$arguments" optional="yes" />
			<string name="filterValue" scope="$arguments" optional="yes" />
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
	$datetime = $doc->find('td.col-datetime div.col-datetime')->text();
	ob_start();
	?><div class="col-date"><?php echo date('Y-m-d', strtotime($datetime)); ?></div><?php
	?><div class="col-time small text-muted"><?php echo date('H:i:s', strtotime($datetime)); ?></div><?php
	$doc->find('td.col-datetime div.col-datetime')->after( ob_get_clean() )->remove();
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