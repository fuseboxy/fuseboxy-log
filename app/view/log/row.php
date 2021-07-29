<?php /*
<fusedoc>
	<io>
		<in>
			<object name="$bean">
				<string name="action" />
				<datetime name="datetime" />
				<string name="username" />
				<string name="sim_user" />
				<string name="entity_type" />
				<number name="entity_id" />
				<string name="remark" />
				<string name="ip" />
			</object>
			<string name="filterField" scope="$arguments" optional="yes" />
			<string name="filterValue" scope="$arguments" optional="yes" />
		</in>
		<out />
	</io>
</fusedoc>
*/ ?>
<div id="log-row-<?php echo $bean->id; ?>" class="log-row scaffold-row small">
	<table class="table table-hover table-sm mb-0">
		<tbody>
				<td width="60" class="col-id"><?php echo $bean->id; ?></td>
				<td width="13%" class="col-datetime">
					<div class="col-date"><?php echo date('Y-m-d', strtotime($bean->datetime)); ?></div>
					<div class="col-time small text-muted"><?php echo date('H:i:s', strtotime($bean->datetime)); ?></div>
				</td>
				<td width="13%" class="col-username-sim_user">
					<div class="col-username"><?php echo $bean->username; ?></div>
					<div class="col-sim_user small text-muted"><?php echo $bean->sim_user; ?></div>
				</td>
				<td width="13%" class="col-action-ip">
					<div class="col-action"><?php echo $bean->action; ?></div>
					<div class="col-ip small text-muted"><?php echo $bean->ip; ?></div>
				</td>
				<td width="13%" class="col-entity_id-entity_type">
					<div class="col-entity_id"><?php echo $bean->entity_id; ?></div>
					<div class="col-entity_type small text-muted"><?php echo $bean->entity_type; ?></div>
				</td>
				<td width="30%" class="col-remark" style="word-break: break-all;"><?php
					$str = $bean->remark;
					if ( isset($arguments['filterField']) and $arguments['filterField'] == 'remark' and !empty($arguments['filterValue']) ) {
						$startPos = stripos($str, $arguments['filterValue']);
						$endPos = stripos($str, $arguments['filterValue']) + strlen($arguments['filterValue']);
						$str = substr($str, 0, $endPos).'</mark>'.substr($str, $endPos);
						$str = substr($str, 0, $startPos).'<mark>'.substr($str, $startPos);
					}
					echo nl2br($str);
				?></td>
				<td class="col-button text-right"><?php include F::appPath('view/scaffold/row.button.php'); ?></td>
			</tr>
		</tbody>
	</table>
</div>