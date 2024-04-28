<?php
// require_once PORTAL_URI . 'includes/class-db-connection.php';
// require_once PORTAL_URI . 'admin/objects/class-parents.php';

// $db = new Database();

// $parent = new Parents($db->conn);

// if ( isset($_GET['parent_action']) ) {
//   if ( $_GET['parent_action'] === 'sync' )
// 	  echo '<h4>Syncing Data</h4>';
  
//   if ( $_GET['parent_action'] === 'delete' )
// 	  echo '<h4>Deleting Data</h4>';
// }

?>
<div class="container">
    <section class="data-sync">
        <h4>Data Sync</h4>
        <a class="button button-primary" href="<?php echo admin_url('/options-general.php?page=comworks_ab_portal&tab=parents&parent_action=sync&psync_page=0') ?>">Pull Parents data from Portal</a>
        <a class="button button-danger" href="<?php echo admin_url('/options-general.php?page=comworks_ab_portal&tab=parents&parent_action=delete') ?>">Delete data from Portal</a>
    <section>
    <section>
        <h4>Send Email Settings</h4>
        <form method="post" action="options.php">
            <?php settings_fields( 'portal_email_settings' ); ?>
            <?php do_settings_sections( 'comworks_ab_portal' ); ?>
            <div class="form-control">
                <label for="pes_email_from">Email From</label>
                <input type="text" name="pes_email_from" value="<?php echo esc_attr( get_option( 'pes_email_from' ) ); ?>">
            </div>
            <div class="form-control">
                <label for="pes_email_from_text">Email From Text</label>
                <input type="text" name="pes_email_from_text" value="<?php echo esc_attr( get_option( 'pes_email_from_text' ) ); ?>">
            </div>
            <div class="form-control">
                <label for="pes_email_subject">Email Subject</label>
                <input type="text" name="pes_email_subject" value="<?php echo esc_attr( get_option( 'pes_email_subject' ) ); ?>">
            </div>
            <div class="form-control">
                <label for="pes_login_link">Login Link (relative path)</label>
                <input type="text" name="pes_login_link" value="<?php echo esc_attr( get_option( 'pes_login_link' ) ); ?>">
            </div>
            <div class="form-control">
                <div class="full">
                    <label for="pes_email_message">
                        <p>Email Message</p>
                        <p>You can use the following shortcodes for dynamic content on the message:</p>
                        <p><code>[name]</code>, <code>[username]</code>, <code>[password]</code>, <code>[login_link]</code></p>
                    </label>
                </div>
                <div class="full">
                    <textarea id="pes_email_message" name="pes_email_message" rows="6" cols="62"><?php echo esc_attr( get_option( 'pes_email_message' ) ); ?></textarea>
                </div>
            </div>
            <?php submit_button(); ?>
        </form>
    </section>
    <section>
        <h4>Dashboard Settings</h4>
        <form method="post" action="options.php">
            <?php settings_fields( 'portal_dashboard_settings' ); ?>
            <?php do_settings_sections( 'comworks_ab_portal' ); ?>
            <div class="form-control">
                <label for="pd_title_text">Dashboard Title Text</label>
                <input type="text" name="pd_title_text" value="<?php echo esc_attr( get_option( 'pd_title_text' ) ); ?>">
            </div>
            <div class="form-control">
                <label for="pd_welcome_text">Dashboard Welcome Text</label>
                <input type="text" name="pd_welcome_text" value="<?php echo esc_attr( get_option( 'pd_welcome_text' ) ); ?>">
            </div>
            <div class="form-control">
                <label for="pd_desc_text">Dashboard Description Text</label>
                <textarea id="pd_desc_text" name="pd_desc_text" rows="4" cols="50"><?php echo esc_attr( get_option( 'pd_desc_text' ) ); ?></textarea>
            </div>
            <div class="form-control">
                <label for="pd_assess_text">Portal Assessment Title Text</label>
                <input type="text" name="pd_assess_text" value="<?php echo esc_attr( get_option( 'pd_assess_text' ) ); ?>">
            </div>
            <div class="form-control">
                <label for="pd_account_text">Portal My Account Title Text</label>
                <input type="text" name="pd_account_text" value="<?php echo esc_attr( get_option( 'pd_account_text' ) ); ?>">
            </div>
            <div class="form-control">
                <label for="pd_contact_text">Portal Contact Us Title Text</label>
                <input type="text" name="pd_contact_text" value="<?php echo esc_attr( get_option( 'pd_contact_text' ) ); ?>">
            </div>
            <?php submit_button(); ?>
        </form>
    </section>
</div>
