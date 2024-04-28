<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once PORTAL_URI . 'includes/class-db-connection.php';
require_once PORTAL_URI . 'admin/objects/class-assessments.php';

$db = new Database();
$assessments_obj = new Assessments($db->conn);

$user_id = get_current_user_id();
$uuid = get_field('portal_uid', 'user_' . $user_id);

$p_assessments = $assessments_obj->get_assessment_by_parent($uuid);

get_header();
$user = new WP_User($user_id);
wc_get_template( 'myaccount/form-edit-account.php', array(
	'user' => $user, // Pass the $user variable to the template
) );
?>

<main id="content" <?php post_class( 'site-main' ); ?>>
    <header class="page-header">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
    </header>
	<div class="page-content">
		<table>
			<thead>
				<tr>
					<th>Student Name</th>
					<th>Skill</th>
					<th>Score</th>
					<th>Centre</th>
					<th>Time</th>
					<th>Assessor</th>
				</tr>
			</thead>
			<tbody>
			<?php
				foreach($p_assessments as $key => $assessment) {
					echo "<tr>";
					echo "<td>{$assessment['FullName']}</td>";
					echo "<td>{$assessment['SkillName']}</td>";
					echo "<td>{$assessment['Score']}</td>";
					echo "<td>{$assessment['CentreName']}</td>";
					echo "<td>{$assessment['TimeStamp']}</td>";
					echo "<td>{$assessment['Username']}</td>";
					echo "</tr>";
				}	
			?>
			</tbody>
		</table>
	</div>
</main>

<?php

get_footer();
