<?php /*
<fusedoc>
	<io>
		<in>
			<structure name="$xfa">
				<string name="search" optional="yes" />
				<string name="reset" optional="yes" />
			</structure>
			<number name="$logCount" optional="yes" />
			<array name="$logUsers" optional="yes">
				<string name="+" />
			</array>
			<array name="$logActions" optional="yes">
				<string name="+" />
			</array>
			<structure name="search" scope="$arguments" optional="yes" comments="submitted search criteria">
				<string name="datetime_keyword" />
				<string name="username" />
				<string name="action" />
				<string name="entity_id" />
				<string name="remark_keyword" />
				<string name="ip_keyword" />
			</structure>
		</in>
		<out>
			<structure name="search" scope="form" optional="yes" oncondition="xfa.search">
				<string name="datetime_keyword" />
				<string name="username" />
				<string name="action" />
				<string name="entity_id" />
				<string name="remark_keyword" />
				<string name="ip_keyword" />
			</structure>
		</out>
	</io>
</fusedoc>
*/ ?>
<form
	id="log-search"
	class="form-horizontal"
	method="post"
	action="<?php echo F::url($xfa['search']); ?>"
>
	<table class="table table-condensed" style="margin-bottom: 0;">
		<thead>
			<tr class="active">
				<td width="5%" class="col-id">
					<?php if ( isset($logCount) ) : ?>
						<small class="text-danger">
							<?php echo $logCount; ?> item<?php if ( $logCount > 1 ) echo 's'; ?>
						</small>
					<?php endif; ?>
				</td>
				<td width="14%" class="col-datetime">
					<input
						type="text"
						class="form-control input-sm"
						name="search[datetime_keyword]"
						value="<?php echo isset($arguments['search']['datetime_keyword']) ? $arguments['search']['datetime_keyword'] : ''; ?>"
					/>
				</td>
				<td width="14%" class="col-username">
					<?php if ( !empty($logUsers) ) : ?>
						<select
							name="search[username]"
							class="form-control input-sm <?php if ( !empty($arguments['search']['username']) ) echo 'alert-warning'; ?>"
						>
							<option value=""></option>
							<?php foreach ( $logUsers as $username ) : ?>
								<?php $selected = ( !empty($arguments['search']['username']) and $arguments['search']['username'] == $username ); ?>
								<option value="<?php echo $username; ?>" <?php if ( $selected ) echo 'selected' ?>>
									<?php echo $username; ?>
								</option>
							<?php endforeach; ?>
						</select>
					<?php endif; ?>
				</td>
				<td width="14%" class="col-action">
					<?php if ( !empty($logActions) ) : ?>
						<select
							name="search[action]"
							class="form-control input-sm <?php if ( !empty($arguments['search']['action']) ) echo 'alert-warning'; ?>"
						>
							<option value=""></option>
							<?php foreach ( $logActions as $action ) : ?>
								<?php $selected = ( !empty($arguments['search']['action']) and $arguments['search']['action'] == $action ); ?>
								<option value="<?php echo $action; ?>" <?php if ( $selected ) echo 'selected' ?>>
									<?php echo $action; ?>
								</option>
							<?php endforeach; ?>
						</select>
					<?php endif; ?>
				</td>
				<td width="10%" class="col-entity">
					<input
						type="text"
						name="search[entity_id]"
						class="form-control input-sm <?php if ( !empty($arguments['search']['entity_id']) ) echo 'alert-warning'; ?>"
						value="<?php echo isset($arguments['search']['entity_id']) ? $arguments['search']['entity_id'] : ''; ?>"
					/>
				</td>
				<td width="25%" class="col-remark">
					<input
						type="text"
						class="form-control input-sm"
						name="search[remark_keyword]"
						value="<?php echo isset($arguments['search']['remark_keyword']) ? $arguments['search']['remark_keyword'] : ''; ?>"
					/>
				</td>
				<td width="10%" class="col-ip">
					<input
						type="text"
						class="form-control input-sm"
						name="search[ip_keyword]"
						value="<?php echo isset($arguments['search']['ip_keyword']) ? $arguments['search']['ip_keyword'] : ''; ?>"
					/>
				</td>
				<td class="col-button text-right" style="white-space: nowrap;">
					<?php if ( isset($xfa['reset']) ) : ?>
						<a
							href="<?php echo F::url($xfa['reset']); ?>"
							class="btn btn-sm btn-inverse"
						><i class="fa fa-times"></i> Reset</a>
					<?php endif; ?>
					<button
						type="submit"
						class="btn btn-sm btn-default"
					><i class="fa fa-search"></i> Search</button>
				</td>
			</tr>
		</thead>
	</table>
</form>