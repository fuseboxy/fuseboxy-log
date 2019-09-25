<?php /*
<fusedoc>
	<io>
		<in>
			<string name="filterField" scope="$arguments" />
			<string name="filterValue" scope="$arguments" optional="yes" />
			<number name="$totalRecordCount" optional="yes" />
		</in>
		<out>
			<string name="filterField" scope="url" />
			<string name="filterValue" scope="url" />
		</out>
	</io>
</fusedoc>
*/ ?>
<form id="log-search" method="get" action="<?php echo F::url(); ?>">
	<!-- query string -->
	<?php foreach ( $_GET as $key => $val ) : ?>
		<?php if ( !in_array($key, array('filterValue', 'page')) ) : ?>
			<input type="hidden" name="<?php echo $key; ?>" value="<?php echo $val; ?>" />
		<?php endif; ?>
	<?php endforeach; ?>
	<!-- fields -->
	<div class="form-group">
		<div class="input-group">
			<input 
				type="text" 
				class="form-control" 
				aria-label="Search..." 
				placeholder="Search..." 
				name="filterValue"
				value="<?php if ( isset($arguments['filterValue']) ) echo trim($arguments['filterValue']); ?>"
				autofocus
			/>
			<div class="input-group-append">
				<button type="submit" class="btn btn-light border"><i class="fa fa-search"></i></button>
			</div>
		</div>
		<!-- record count -->
		<?php if ( isset($totalRecordCount) ) : ?>
			<small class="form-text text-muted ml-1">
				<?php echo empty($totalRecordCount) ? 'No' : $totalRecordCount; ?>
				record<?php if ( $totalRecordCount > 1 ) echo 's'; ?>
				found
			</small>
		<?php endif; ?>
	</div><!--/.form-group-->
</form>