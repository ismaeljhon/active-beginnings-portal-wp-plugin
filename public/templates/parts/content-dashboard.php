<?php
$user_id = get_current_user_id();
$user = new WP_User($user_id);

$assessment_link = isset($user->roles) && $user->roles[0] == 'centre_user_role' ? '?tab=centre-view&active=Y' : ( isset($user->roles) && $user->roles[0] == 'ab_admin' ? '?tab=centre-students&active=Y' : '?tab=students');
?>
<section class="dashboard">
    <div class="container">
        <div class="heading">
            <h6><?php echo get_option( 'pd_welcome_text' ) ?></h6>
            <h2><?php echo get_option( 'pd_title_text' ); ?></h2>
            <p><?php echo get_option( 'pd_desc_text' ); ?></p>
        </div>
        <div class="content">
            
            <div class="blurb">
                <a href="<?php echo $assessment_link; ?>">
                    <img width="80" height="70" src="<?php echo PORTAL_URL . 'assets/img/icon-graph.svg'?>" alt="icon-dashboard" />
                    <span><?php echo get_option( 'pd_assess_text' ); ?></span>
                </a>
            </div>
            <?php if ( isset($user->roles) ): ?>
                <?php if ( $user->roles[0] == 'centre_user_role' ): ?>
                    <div class="blurb">
                        <a href="?tab=centre-report">
                            <img width="80" height="70" src="<?php echo PORTAL_URL . 'assets/img/icon-graph.svg'?>" alt="icon-dashboard" />
                            <span>Centre Report</span>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="blurb">
                        <a href="?tab=my-account">
                            <img width="80" height="70" src="<?php echo PORTAL_URL . 'assets/img/icon-user.svg'?>" alt="icon-dashboard" />
                            <span><?php echo get_option( 'pd_account_text' ); ?></span>
                        </a>
                    </div>
                    <div class="blurb">
                        <a href="?tab=contact-us">
                            <img width="80" height="70" src="<?php echo PORTAL_URL . 'assets/img/icon-email.svg'?>" alt="icon-email" />
                            <span><?php echo get_option( 'pd_contact_text' ); ?></span>
                        </a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</section>