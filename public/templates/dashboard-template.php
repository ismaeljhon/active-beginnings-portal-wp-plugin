<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( !is_user_logged_in() ) {
    wp_redirect( home_url( '/login/' ) );
    exit;
}

require_once PORTAL_URI . 'includes/class-db-connection.php';
require_once PORTAL_URI . 'admin/objects/class-assessments.php';

$db = new Database();
$assessments_obj = new Assessments($db->conn);

$user_id = get_current_user_id();
$uuid = get_field('portal_uid', 'user_' . $user_id);
$user = new WP_User($user_id);

$p_assessments = $assessments_obj->get_assessment_by_parent($uuid);
$tab = $_GET['tab'] ?? null;

if ( 
    isset($user->roles) 
    && ($user->roles[0] == 'parent_role' || $current_user->roles[0] == 'centre_user_role') 
    && get_user_meta( $user_id, 'first_login', true ) != '1' 
) {
    $tab = 'password-change';
}

get_header();
?>

<main id="content" <?php post_class( 'site-main' ); ?>>
	<div class="page-content">
        <div class="container">
            <div class="tab-vertical">
                <nav class="nav-tab-wrapper">
					<input type="checkbox" id="nav-check">
					<div class="nav-btn">
						<label for="nav-check">
						  <span></span>
						  <span></span>
						  <span></span>
						</label>
					</div>
					<div class="nav-list">
                        <div class="nav-item">
                            <a href="?tab=dashboard" class="nav-tab <?php if( $tab == null || $tab == 'dashboard' ):?>nav-tab-active<?php endif; ?>">
                                <img width="20" src="<?php echo PORTAL_URL . 'assets/img/icon-dashboard.svg'?>" alt="icon-dashboard" />
                                <span>Dashboard</span>
                            </a>
                        </div>
                        <?php if ( isset($user->roles) && $user->roles[0] == 'parent_role') : ?>
                            <div class="nav-item">
                                <a href="?tab=students" class="nav-tab <?php if( $tab == 'students' ):?>nav-tab-active<?php endif; ?>">
                                    <img width="20" src="<?php echo PORTAL_URL . 'assets/img/icon-graph.svg'?>" alt="icon-graph" />
                                    <span>Assessments</span>
                                </a>
                            </div>
                        <?php endif; ?>
                        <?php if ( isset($user->roles) && $user->roles[0] == 'centre_user_role') : ?>
                            <div class="nav-item">
                                <a href="?tab=centre-view&active=Y" class="nav-tab <?php if( $tab == 'centre-view' ):?>nav-tab-active<?php endif; ?>">
                                    <img width="20" src="<?php echo PORTAL_URL . 'assets/img/icon-graph.svg'?>" alt="icon-graph" />
                                    <span>Assessments</span>
                                </a>
                                <?php $active = $_GET['active'] ?? '';?>
                                <?php if( $tab == 'centre-view' ) : ?>
                                    <ul>
                                        <li>
                                            <a href="?tab=centre-view&active=Y" class="nav-tab <?php if( $active == 'Y' ):?>nav-tab-active<?php endif; ?>">
                                                <span>Active</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="?tab=centre-view&active=N" class="nav-tab <?php if( $active == 'N' ):?>nav-tab-active<?php endif; ?>">
                                                <span>Inactive</span>
                                            </a>
                                        </li>
                                    </ul>
                                <?php endif; ?>
                            </div>
                            <div class="nav-item">
                                <a href="?tab=centre-report" class="nav-tab <?php if( $tab == 'centre-report' ):?>nav-tab-active<?php endif; ?>">
                                    <img width="20" src="<?php echo PORTAL_URL . 'assets/img/icon-graph.svg'?>" alt="icon-graph" />
                                    <span>Centre Reports</span>
                                </a>
                            </div>
                        <?php endif; ?>
                        <?php if ( isset($user->roles) && $user->roles[0] == 'ab_admin') : ?>
                            <div class="nav-item">
                                <a href="?tab=centre-students&active=y" class="nav-tab <?php if( $tab == 'centre-students' ):?>nav-tab-active<?php endif; ?>">
                                    <img width="20" src="<?php echo PORTAL_URL . 'assets/img/icon-graph.svg'?>" alt="icon-graph" />
                                    <span>Assessments</span>
                                </a>
                                <?php $active = $_GET['active'] ?? '';?>
                                <?php if( $tab == 'centre-students' ) : ?>
                                    <ul>
                                        <li>
                                            <a href="?tab=centre-students&active=Y" class="nav-tab <?php if( $active == 'Y' ):?>nav-tab-active<?php endif; ?>">
                                                <span>Active</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="?tab=centre-students&active=N" class="nav-tab <?php if( $active == 'N' ):?>nav-tab-active<?php endif; ?>">
                                                <span>Inactive</span>
                                            </a>
                                        </li>
                                    </ul>
                                <?php endif; ?>
                            </div>
                            <div class="nav-item">
                                <a href="?tab=centre-lists" class="nav-tab <?php if ( $tab == 'centre-lists' || $tab == 'gross-motor-report' || $tab == 'gmp-list' || $tab == 'centre-report' || $tab == 'gmp-plans' ) : ?>nav-tab-active<?php endif; ?>">
                                    <img width="20" src="<?php echo PORTAL_URL . 'assets/img/icon-graph.svg'?>" alt="icon-graph" />
                                    <span>Centre</span>
                                </a>
                                <?php if ( $tab == 'centre-lists' || $tab == 'gmp-list' || $tab == 'gross-motor-report' || $tab == 'centre-report' || $tab == 'gmp-plans' ): ?>
                                    <ul>
                                        <li>
                                            <a href="?tab=centre-lists" class="nav-tab <?php if ( $tab == 'centre-lists' || $tab == 'centre-report' ):?>nav-tab-active<?php endif; ?>">
                                                <span>Centre Report</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="?tab=gmp-list" class="nav-tab <?php if ( $tab == 'gross-motor-report' || $tab == 'gmp-list' ):?>nav-tab-active<?php endif; ?>">
                                                <span>Gross Motor Plan</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="?tab=gmp-plans" class="nav-tab <?php if ( $tab == 'gmp-plans' ):?>nav-tab-active<?php endif; ?>">
                                                <span>GMP Reports</span>
                                            </a>
                                        </li>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="nav-item">
                            <a href="?tab=my-account" class="nav-tab <?php if ( $tab == 'my-account' ): ?>nav-tab-active<?php endif; ?>">
                                <img width="20" src="<?php echo PORTAL_URL . 'assets/img/icon-user.svg'?>" alt="icon-user" />
                                <span>My Account</span>
                            </a>
                            <?php if ($tab == 'my-account'): ?>
                                <ul>
                                    <?php if ($user->roles[0] == 'parent_role'): ?>
                                        <li>
                                            <a href="?tab=student-registration" class="nav-tab">
                                                <span>Registration Form</span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <li>
                                        <a href="?tab=my-account" class="nav-tab <?php if ($tab == 'my-account'): ?>nav-tab-active<?php endif; ?>">
                                            <span>Account Details</span>
                                        </a>
                                    </li>
                                </ul>
                            <?php endif; ?>
                        </div>
                        <div class="nav-item">
                            <a href="?tab=contact-us" class="nav-tab <?php if( $tab == 'contact-us' ):?>nav-tab-active<?php endif; ?>">
                                <img width="20" src="<?php echo PORTAL_URL . 'assets/img/icon-email.svg'?>" alt="icon-email" />
                                <span>Contact Us</span>
                            </a>
                        </div>
                        <div class="nav-item">
                            <a class="nav-tab nav-logout" href="<?php echo wp_logout_url( get_permalink() ); ?>">
                                <img width="20" src="<?php echo PORTAL_URL . 'assets/img/icon-logout.svg'?>" alt="icon-logout" />
                                <span>Logout</span>
                            </a>
                        </div>
					</div>
                </nav>
                <div class="tab-content">
                    <div class="container">
                    <?php switch($tab) :
                        case 'assessments':
                            require_once PORTAL_URI . 'public/templates/parts/content-student-assessment.php';
							break;
						case 'students':
							require_once PORTAL_URI . 'public/templates/parts/content-students.php';
							break;
                        case 'student-profile':
                            require_once PORTAL_URI . 'public/templates/parts/content-student-profile.php';
                            break;
                        case 'centre-students':
                            require_once PORTAL_URI . 'public/templates/parts/content-centre-students.php';
							break;
						case 'centre-report':
                            require_once PORTAL_URI . 'public/templates/parts/content-centre-report.php';
                            break;
                        case 'gmp-list':
                            require_once PORTAL_URI . 'public/templates/parts/content-gmp-centre-list.php';
                            break;
                        case 'gross-motor-report':
                            $action = $_GET['action'] ?? '';
                            if ( $action != '' ) {
                                $gmp_templates = array(
                                    'skills'        => 'content-gmp-skills-select.php',
                                    'assign'        => 'content-gmp-assign-staff.php',
                                    'general-note'  => 'content-gmp-general-note.php',
                                    'done'          => 'content-gmp-done.php',
                                    'student'       => 'content-gmp-student.php',
                                    'student-done'  => 'content-gmp-student-done.php',
                                    'review'        => 'content-gmp-review-report.php'
                                );

                                require_once PORTAL_URI . 'public/templates/parts/' . $gmp_templates[$action];
                                break;
                            }
                            
                            require_once PORTAL_URI . 'public/templates/parts/content-gmp-skills-report.php';
                            break;
                        case 'gmp-plans':
                            require_once PORTAL_URI . 'public/templates/parts/content-gmp-centre-reports.php';
                            break;
                        case 'centre-view':
                            require_once PORTAL_URI . 'public/templates/parts/content-centre-view.php';
                            break;
                        case 'centre-lists':
                            require_once PORTAL_URI . 'public/templates/parts/content-centre-list.php';
                            break;
                        case 'my-account':
                            require_once PORTAL_URI . 'public/templates/parts/content-account.php';
                            break;
                        case 'student-registration':
                            require_once PORTAL_URI . 'public/templates/parts/content-student-registration.php';
                            break;
                        case 'password-change':
                            echo "<p class='warning'>Please change your password and update your details to proceed using the site</p>";
                            require_once PORTAL_URI . 'public/templates/parts/content-account.php';
                            break;
                        case 'contact-us':
                            echo "<h2>Contact Us</h2>";
                            echo do_shortcode("[gravityform id='1' title='false' ajax='true']");
                            break;
                        case 'report':
                            echo 'test';
                            require_once PORTAL_URI . 'public/templates/parts/content-pdf-report.php';
                            break;
                        default:
							require_once PORTAL_URI . 'public/templates/parts/content-dashboard.php';
                            break;
                        endswitch;
                    ?>
                    </div>
                </div>
            </div>
        </div> 
	</div>
</main>

<?php

get_footer();
