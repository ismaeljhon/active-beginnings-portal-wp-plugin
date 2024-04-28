<?php
require_once PORTAL_URI . 'includes/class-db-connection.php';
require_once PORTAL_URI . 'admin/objects/class-centres.php';

$db = new Database();
$centre_obj = new Centres($db->conn);

if (isset($_GET['centre_id'])) {
    $centre_id = $_GET['centre_id'];
    $centre = $centre_obj->get_centre($centre_id);
    $centre_id = $centre['CentreID'];
} else {
    $user_id = get_current_user_id();
    $uuid = get_field('user_uid', 'user_' . $user_id);
    $centre = $centre_obj->get_centre($uuid);
    $centre_id = $centre['CentreID'];
}

$centre_report = $centre_obj->get_centre_reports($centre_id);

$skills = array();
foreach($centre_report as $report) {
    $slug = str_replace(' ', '_', strtolower($report['skillName']));
    $skills[$slug][] = $report;
}


?>
<html>
<head>
    <title>Center Summary Report: <?php echo date('M Y'); ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap');
        @page { 
            margin: 30px 20px 40px; 
        }
        header {
            position: fixed;
            left: 0;
            right: 0;
            top: 0;
            height: 30px;
            margin-top: -50px;
        }
        header p {
            color: #999;
            display: block;
            padding-bottom: 10px;
            text-align: right;
            text-decoration: underline;
        }
        footer {
            position: fixed;
            left: 0px;
            right: auto;
            height: 60px;
            bottom: 30px;
            margin-bottom: -50px;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
        }
        body { 
            margin: 30px 0; 
        }
        body * {
            font-family: 'Inter', sans-serif;
        }
        .container {
            max-width: 1080px !important;
            box-shadow: none !important;
        }
        .heading {
            text-align: center;
            padding-bottom: 50px;
        }
        .heading .col {
            display: inline-block;
            width: 20%;
            height: 100px;
            vertical-align: baseline;
        }
        .heading .col:nth-child(2) {
            width: 55%;
        }
        .heading h2 {
            color: #294146;
            font-family: 'Poppins', sans-serif;
            font-size: 35px;
            font-weight: 600;
            line-height: 1em;
        }
        .heading h6 {
            color: #6DB2FF;
            font-family: 'Poppins', sans-serif;
            font-size: 24px;
            font-weight: 400;
            line-height: 10px;
        }
        .student-assessment h6 {
            color: #6db2ff;
            font-family: 'Poppins', sans-serif;
            font-size: 18px !important;
            font-weight: 400 !important;
            margin: 0;
        }
        .student-assessment p {
            display: block;
            text-align: center;
        }
        .centre-report .skill {
            border-bottom: 1px solid #e3e3e3;
            padding: 20px 0 30px;
        }
        .centre-report .skill .data {
            border-top: 1px solid #999;
            border-left: 1px solid #999;
            width: 550px;
        }
        .centre-report .skill .data th,
        .centre-report .skill .data td {
            border-bottom: 1px solid #999;
            border-right: 1px solid #999;
            font-size: 12px;
            padding: 2px;
        }
        .centre-report .skill .data th:not(:first-child),
        .centre-report .skill .data td:not(:first-child) {
            border-left: 1px solid #999;
            text-align: right;
        }
        .centre-report .skill td img {
            padding: 0 0 0 4px;
            vertical-align: bottom;
        }
        .centre-report .skill td img.down {
            padding: 0 4px 0 0;
            transform: rotate(180deg);
        }
        .centre-report .skill .comments {
            font-size: 16px;
        }
        .centre-report .report-graph {
            display: block;
            margin: 40px 0;
            height: 200px;
            padding-left: 20px;
            padding-bottom: 11px;
            position: relative;
        }
        .graph-stats {
            background: url('https://www.funfitkidz.com.au/site/wp-content/plugins/active-beginnings-portal/assets/img/bg-graph-pdf.jpg') no-repeat;
            background-size: cover;
            display: table;
            width: 100%;
            height: 200px;
            padding-left: 40px;
            position: relative;
            z-index: 10;
        }
        .row {
            display: table-row;
        }
        .stat {
            display: table-cell;
            height: 200px;
            position: relative;
            padding-bottom: 11px;
            vertical-align: bottom;
        }
        .stat span {
            position: absolute;
            bottom: -34px;
            left: 0;
            right: 0;
        }
        .stat .bar {
            font-size: 12px;
            background-color: #ffd480;
            color: #000;
            display: inline-block;
            margin-right: 2px;
            padding: 5px;
            vertical-align: bottom;
        }
        .stat .bar2 {
            background: #da81f6;
        }
        .stat .bar3 {
            background: #58acfa;
        }
        .graph-legends {
            text-align: center;
        }
        .graph-legends span {
            margin-right: 20px;
        }
        .graph-legends span:before {
            content: "";
            background-color: #ffd480;
            display: inline-block;
            height: 15px;
            margin-right: 10px;
            vertical-align: top;
            width: 15px;
        }
        .graph-legends span:nth-child(2):before {
            background: #da81f6;
        }
        .graph-legends span:nth-child(3):before {
            background: #58acfa;
        }
        .comments {
            background: #ddd;
            border-radius: 15px;
            padding: 5px 10px;
        }
        .comments p {
            font-size: 14px;
        }

        .wrapper-page {
            page-break-after: always;
        }

        .wrapper-page:last-child {
            page-break-after: avoid;
        }
        </style>
</head>
<body>
    <header>
      <p><b>Active Beginnings Centre Report for <?php echo $centre['Name'] ?></b></p>
    </header>
    <footer>
      <p><img width="100" height="60" src="https://www.funfitkidz.com.au/site/wp-content/uploads/2023/06/funfit_white_bkgd-300x208.png" alt="funfit-logo"></p>
    </footer>
    <section class="student-assessment wrapper-page">
        <div class="container">
            <div class="heading">
                <div class="col">
                <img width="200" height="110" src="https://www.funfitkidz.com.au/site/wp-content/uploads/2023/06/funfit_white_bkgd-300x208.png" alt="funfit-logo">
                </div>
                <div class="col">
                    <h2><?php echo $centre['Name'] ?></h2>
                    <h6>Center Summary Report: <?php echo date('M Y') ?></h6>
                </div>
                <div class="col">
                <img width="180" height="90" src="https://www.activebeginnings.com.au/site/wp-content/uploads/main-logo.png" alt="active-logo">
                </div>
            </div>
        </div>
    </section>
    <section class="centre-report">
    <?php foreach($skills as $skill): ?>
        <div class="skill wrapper-page">
            <h4><?php echo $skill[0]['skillName']; ?></h4>
            <table class="data">
                <thead>
                    <tr>
                        <th>Age Bracket</th>
                        <th>Age Benchmark</th>
                        <th><?php echo date('Y') ?> Count</th>
                        <th><?php echo date('Y') ?> Average</th>
                        <th>Centre Count</th>
                        <th>Centre Average</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($skill as $skill_rep): ?>
                    <tr>
                        <td data-label="Age Bracket"><?php echo $skill_rep['caAgeGrp']; ?></td>
                        <td data-label="Age Benchmark"><?php echo $skill_rep['ncScore']; ?></td>
                        <td data-label="<?php echo date('Y') ?> Count"><?php echo $skill_rep['cyCount']; ?></td>
                        <td data-label="<?php echo date('Y') ?> Average">
                        <?php
                            echo $skill_rep['cyScore']; 
                            $arrow_src = $skill_rep['cyScore'] > $skill_rep['ncScore'] ? 'assets/img/icon-arrow-up.svg' : 'assets/img/icon-arrow-down1.png';
                        ?>
                            <img width="20" src="<?php echo PORTAL_URL . $arrow_src; ?>" />
                        </td>
                        <td data-label="Centre Count"><?php echo $skill_rep['caCount']; ?></td>
                        <td data-label="Centre Average">
                        <?php
                            echo $skill_rep['caScore'];
                            $arrow_class = $skill_rep['caScore'] > $skill_rep['ncScore'] ? 'up' : 'down';
                        ?>
                            <img width="20" src="<?php echo PORTAL_URL . $arrow_src; ?>" />
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <h6>Centre Comparisons vs Overall Age Group</h6>
            <div class="report-graph">
                <div class="graph-stats">
                    <div class="row">
                    <?php foreach($skill as $skill_rep): ?>
                        <div class="stat">
                            <div class="bar bar1" style="height:<?php echo $skill_rep['ncScore'] - 5; ?>%;" >
    							<?php echo $skill_rep['ncScore']; ?>
    						</div>
                            <div class="bar bar2" style="height:<?php echo $skill_rep['cyScore'] - 5; ?>%;" >
    							<?php echo $skill_rep['cyScore']; ?>
    						</div>
                            <div class="bar bar3" style="height:<?php echo $skill_rep['caScore'] - 5; ?>%;" >
    							<?php echo $skill_rep['caScore']; ?>
    						</div>
    						<span><?php echo $skill_rep['caAgeGrp']; ?></span>
                        </div>
                    <?php endforeach; ?>
                    </div>
                </div>
            </div>
    		<div class="graph-legends">
                <p>
                    <span>Age Benchmark</span>
                    <span><?php echo date('Y') ?> Center Average</span>
                    <span>Center Average</span>
                </p>
            </div>
            <div class="comments">
                <p><b>Description:</b></p>
                <p><?php echo $skill[0]['skillDescription']; ?> </p>
            </div>
        </div>
    <?php endforeach; ?>
    </section>
</body>
</html>