<div id="log-row-<?php echo $bean->id; ?>" class="log-row small">
	<table class="table table-hover table-condensed" style="margin-bottom: 0;">
		<tbody>
			<tr>
				<td width="5%" class="col-id"><sup class="text-muted"><?php echo $bean['id']; ?></sup></td>
				<td width="14%" class="col-datetime">
					<?php echo date('Y-m-d', strtotime($bean['datetime'])); ?>
					<small class="text-muted">@ <?php echo date('H:i', strtotime($bean['datetime'])); ?></small>
				</td>
				<td width="14%" class="col-username">
					<?php echo $bean['username']; ?>
					<?php if ( !empty($bean['sim_user']) ) : ?>
						&nbsp;<small class="text-muted">as</small>&nbsp;
						<code><?php echo $bean['sim_user']; ?></code>
					<?php endif; ?>
				</td>
				<td width="14%" class="col-action"><?php echo $bean['action']; ?></td>
				<td width="10%" class="col-entity">
					<?php if ( !empty($bean['entity_id']) ) : ?>
						<?php echo $bean['entity_id']; ?>
						&nbsp;
						<code><?php echo $bean['entity_type']; ?></code>
					<?php endif; ?>
				</td>
				<td width="25%" class="col-remark"><?php
					$str = $bean['remark'];
					if ( !empty($arguments['search']['remark_keyword']) ) {
						$startPos = stripos($str, $arguments['search']['remark_keyword']);
						$endPos = stripos($str, $arguments['search']['remark_keyword']) + strlen($arguments['search']['remark_keyword']);
						$str = substr($str, 0, $endPos).'</span>'.substr($str, $endPos);
						$str = substr($str, 0, $startPos).'<span style="background-color: yellow;">'.substr($str, $startPos);
					}
					echo nl2br($str);
				?></td>
				<td width="10%" class="col-ip"><?php echo $bean['ip']; ?></td>
				<td class="col-button text-right">&nbsp;</td>
			</tr>
		</tbody>
	</table>
</div>