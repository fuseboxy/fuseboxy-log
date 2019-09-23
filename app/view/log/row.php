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
			<structure name="search" scope="$arguments" optional="yes">
				<string name="remark_keyword" optional="yes" />
			</structure>
		</in>
		<out />
	</io>
</fusedoc>
*/ ?>
<div id="log-row-<?php echo $bean->id; ?>" class="log-row scaffold-row small">
	<table class="table table-hover table-sm mb-0">
		<tbody>
				<td width="7%" class="col-id"><?php echo $bean->id; ?></td>
				<td width="12%" class="col-datetime">
					<div class="col-date"><?php echo date('Y-m-d', strtotime($bean->datetime)); ?></div>
					<div class="col-time small text-muted"><?php echo date('H:i:s', strtotime($bean->datetime)); ?></div>
				</td>
				<td width="12%" class="col-username-sim_user">
					<div class="col-username"><?php echo $bean->username; ?></div>
					<div class="col-sim_user small text-muted"><?php echo $bean->sim_user; ?></div>
				</td>
				<td width="12%" class="col-action"><?php echo $bean->action; ?></td>
				<td width="12%" class="col-entity_id-entity_type">
					<div class="col-entity_id"><?php echo $bean->entity_id; ?></div>
					<div class="col-entity_type small text-muted"><?php echo $bean->entity_type; ?></div>
				</td>
				<td width="25%" class="col-remark"><?php
					$str = $bean->remark;
					if ( !empty($arguments['search']['remark_keyword']) ) {
						$startPos = stripos($str, $arguments['search']['remark_keyword']);
						$endPos = stripos($str, $arguments['search']['remark_keyword']) + strlen($arguments['search']['remark_keyword']);
						$str = substr($str, 0, $endPos).'</mark>'.substr($str, $endPos);
						$str = substr($str, 0, $startPos).'<mark>'.substr($str, $startPos);
					}
					echo nl2br($str);
				?></td>
				<td width="10%" class="col-ip"><?php echo $bean->ip; ?></td>
				<td class="col-button text-right"><?php include F::config('appPath').'view/scaffold/row.button.php'; ?></td>
			</tr>
		</tbody>
	</table>
</div>