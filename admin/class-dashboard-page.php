<?php
class Comworks_Dashboard_Page {
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_dashboard_page' ) );
    }

    public function add_dashboard_page() {
        add_options_page(
            'AB Portal',
            'AB Portal',
            'manage_options',
            'comworks_ab_portal',
            array( $this, 'dashboard_page_callback' ),
        );
    }

    public function dashboard_page_callback() {
        $tab = $_GET['tab'] ?? null;
        ?>
        <div class="wrap comworks-ab-portal">
            <div class="container">
                <h1>Active Beginnings Portal</h1>

                <nav class="nav-tab-wrapper">
                    <a href="?page=comworks_ab_portal" class="nav-tab <?php if ( $tab == null): ?>nav-tab-active<?php endif; ?>">Database</a>
                    <a href="?page=comworks_ab_portal&tab=parents" class="nav-tab <?php if( $tab == 'parents' ):?>nav-tab-active<?php endif; ?>">Parents</a>
                    <a href="?page=comworks_ab_portal&tab=centres" class="nav-tab <?php if( $tab == 'centres' ):?>nav-tab-active<?php endif; ?>">Centre Users</a>
                    <a href="?page=comworks_ab_portal&tab=admins" class="nav-tab <?php if( $tab == 'admins' ):?>nav-tab-active<?php endif; ?>">Admin Users</a>
                </nav>
                <div class="tab-content">
                <?php switch($tab) :
                    case 'parents':
                        require_once PORTAL_URI . 'admin/partials/content-parents.php';
                        break;
                    case 'centres':
                        require_once PORTAL_URI . 'admin/partials/content-centre-users.php';
                        break;
                    case 'admins':
                        require_once PORTAL_URI . 'admin/partials/content-admins.php';
                        break;
                    default:
                        require_once PORTAL_URI . 'admin/partials/content-db-form.php';
                        break;
                    endswitch; 
                ?>
                </div>
            </div>
        </div>
        <?php
    }
}

new Comworks_Dashboard_Page(); 