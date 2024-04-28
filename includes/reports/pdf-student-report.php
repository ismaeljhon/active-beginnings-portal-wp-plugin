<?php
require_once PORTAL_URI . 'includes/class-db-connection.php';
require_once PORTAL_URI . 'admin/objects/class-assessments.php';

$db = new Database();
$assess_obj = new Assessments($db->conn);

$student_id = $_GET['student_id'] ?? null;

if ($student_id) :
    $assessments = $assess_obj->get_assessment_repot($student_id);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Report Card - <?php echo $assessments[0]['FullName']; ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap');
        @page { 
            margin: 60px 20px 70px; 
        }
        header {
            position: fixed;
            left: auto;
            right: 0px;
            top: 0;
            height: 30px;
            margin-top: -40px;
        }
        header p {
            color: #999;
            display: inline-block;
            padding-bottom: 10px;
            text-align: right;
            text-decoration: underline;
        }
        footer {
            position: fixed;
            left: 0px;
            right: auto;
            height: 50px;
            bottom: 0;
            margin-bottom: -20px;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
        }
        body { 
            margin: 60px 0 0; 
            font-family: 'Inter', sans-serif;
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
            width: 30%;
            height: 100px;
            vertical-align: baseline;
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
        .student-assessment .content {
            display: block;
            margin: 60px 0 0;
        }
        .student-assessment .greetings {
            background: #F6F5F0;
            border-radius: 10px;
            padding: 30px 40px;
        }
        .student-assessment .greetings p {
            font-size: 24px;
        }
        .student-assessment .greetings p:last-child {
            font-size: 20px;
        }
        .student-assessment .assessment {
            border: 1px solid #6DB2FF;
            border-radius: 20px;
            display: block;
            margin-top: 10px;
            padding: 20px;
        }
        .student-assessment .assessment h3 {
            color: #6DB2FF;
            font-family: 'Poppins', sans-serif;
            font-size: 40px;
            font-weight: 600;
            line-height: 1.2em;
            text-align: center;
            margin-bottom: 5px;
        }
        .assessment .score {
            display: block;
        }
        .assessment .score .col {
            display: inline-block;
            width: 48%;
        }
        .assessment .score .col:last-child {
            text-align: left;
        }
        .student-assessment .score p {
            font-family: 'Poppins', sans-serif;
            font-size: 20px;
            font-weight: 600;
            text-align: revert;
        }
        .student-assessment .score p span.score {
            background: #C977DD;
            border-radius: 10px;
            color: #fff;
            display: inline-block;
            font-size: 24px;
            font-weight: 600;
            margin: 0 10px;
            padding: 12px 15px;
            text-align: center;
            width: 60px;
        }
        .student-assessment .score p span.avg {
            background: #6DB2FF;
        }
        .student-assessment .score data {
            display: inline-block;
            position: relative;
            top: 5px;
        }
        .student-assessment .score data span {
            display: block;
            font-size: 16px;
            font-weight: 300;
        }
        .student-assessment .score-graph {
            display: flex;
            flex-wrap: wrap;
            gap: 1%;
            max-width: 720px;
            margin: 50px auto 30px;
            align-items: center;
            justify-content: center;
        }
        .student-assessment .score-graph .col {
            display: block;
            width: 100%;
        }
        .student-assessment .score-graph .graph {
            background: #fff url('https://www.funfitkidz.com.au/site/wp-content/plugins/active-beginnings-portal/assets/img/bg-graph.jpg') no-repeat;
            background-size: contain;
            min-height: 200px;
            padding: 0 15px 0 16px;
        }
        .student-assessment .score-graph p {
            font-size: 22px;
            font-family: 'Poppins', sans-serif;
            text-align: left;
        }
        .student-assessment .score-graph .graph-container p {
            margin: 0 0 20px 15px;
        }
        .graph-container .bar {
            background: #6db2ff;
            color: #fff;
            display: block;
            font-family: "Poppins", sans-serif !important;
            font-size: 20px;
            font-weight: 600;
            padding: 10px 20px;
            position: relative;
            top: 25px;
        }
        .graph-container .bar.bar-pink {
            background: #c977dd;
        }
        .skill-description {
            background: #F6F5F0;
            padding: 10px 20px;
            border-radius: 15px;
            text-align: left;
        }
        .skill-description h6 {
            color: #294146;
            font-family: "Poppins", sans-serif;
            font-size: 18px !important;
            font-weight: 600 !important;
            line-height: 1.4em;
            padding-bottom: 15px;
        }
        .skill-description p {
            text-align: left;
            font-family: "Poppins", sans-serif;
            font-size: 15px;
            font-weight: 300;
            line-height: 1.4em !important;
        }
        .student-assessment  p.date {
            font-size: 18px;
            font-weight: 400;
            margin-bottom: 30px;
        }
        .student-assessment p.date span {
            font-weight: 600;
        }

        .wrapper-page {
            page-break-after: always;
        }

        .wrapper-page:last-child {
            page-break-after: avoid;
        }
        </style>
</head>
<body class="student-assessment">
    <header>
      <p><b>Active Beginnings Report Card for <?php echo $assessments[0]['FullName']; ?></b></p>
    </header>
    <footer>
      <p><img width="100" height="60" src="https://www.funfitkidz.com.au/site/wp-content/uploads/2023/06/funfit_white_bkgd-300x208.png" alt="funfit-logo"></p>
    </footer>
    <div class="container">
        <div class="heading">
            <div class="col">
            <img width="200" height="110" src="https://www.funfitkidz.com.au/site/wp-content/uploads/2023/06/funfit_white_bkgd-300x208.png" alt="funfit-logo">
            </div>
            <div class="col">
                <h6>Report Card For</h6>
                <h2><?php echo $assessments[0]['FullName']; ?></h2>
            </div>
            <div class="col">
            <img width="180" height="90" src="https://www.activebeginnings.com.au/site/wp-content/uploads/main-logo.png" alt="active-logo">
            </div>
        </div>
    </div>
    <div class="content">   
        <div class="row">
            <div class="greetings wrapper-page">
                <p>Hi, from the team at Active Beginnings!</p>
                <p><?php echo $assessments[0]['FullName']; ?> has participated in our program and we have assessed them in the following areas:</p>
            </div>
            <?php foreach ($assessments as $assessment) : ?>
                <div class="assessment wrapper-page">
                    <h3><?php echo $assessment['skillName'] ?></h3>
                    <?php $date = date_create($assessment['tmStamp']); ?>
                    <p class="date">Assessment Date: <span><?php echo date_format($date, "F j, Y"); ?></span></p>
                    <div class="score">
                        <div class="col">
                            <p>Your Score <span class="score"><?php echo $assessment['stScore'] ?></span></p>
                        </div>
                        <div class="col">
                            <p>
                                <span class="score avg"><?php echo round($assessment['ageScore'], 2) ?></span>
                                <data>
                                    Age Benchmark
                                    <span>(<?php echo $assessment['ageGrp'] ?>) Age Bracket</span>
                                </data>
                            </p>
                        </div>
                    </div>
                    <div class="score-graph">
                        <div class="col">
                            <div class="graph-container">
                                <p>Scores</p>
                                <p>Assessment Comparison vs Age Group</p>
                                <div class="graph">
                                    <div class="bar bar-pink" style="width: <?php echo $assessment['stScore'] - 6 ?>%;">Your Score</div>
                                    <div class="bar bar-blue"  style="width: <?php echo round($assessment['ageScore'], 2) - 6 ?>% ;">Age Benchmark</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="skill-description">
                        <h6>Description:</h6>
                        <p><?php echo $assessment['skDescription']; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>

<?php
endif;