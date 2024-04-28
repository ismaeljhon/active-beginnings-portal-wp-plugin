<div class="container">
	<h1>Active Beginnings Portal - Database Settings</h1>

	<form method="post" action="options.php">
		<?php settings_fields( 'portal_settings' ); ?>
		<?php do_settings_sections( 'comworks_ab_portal' ); ?>
		<div class="form-control">
			<label for="db_host">DB Host</label>
			<input type="text" name="db_host" value="<?php echo esc_attr( get_option( 'db_host' ) ); ?>">
		</div>
		<div class="form-control">
			<label for="db_name">DB Name</label>
			<input type="text" name="db_name" value="<?php echo esc_attr( get_option( 'db_name' ) ); ?>">
		</div>
		<div class="form-control">
			<label for="db_user">DB User</label>
			<input type="text" name="db_user" value="<?php echo esc_attr( get_option( 'db_user' ) ); ?>">
		</div>
		<div class="form-control">
			<label for="db_password">DB Password</label>
			<input type="password" name="db_password" value="<?php echo esc_attr( get_option( 'db_password' ) ); ?>">
		</div>
		<?php submit_button(); ?>
	</form>
</div>