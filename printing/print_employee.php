<?php
    $conn = mysqli_connect("localhost","gtr-test","qwerty","db_gtr_evalsys");
    if (!$conn) {
        echo "DB connection error" . mysqli_connect_error();
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GT-Evaluation</title>

    <!-- icon  -->
    <link rel="icon" href="./styles/people_icon.svg">
    <!-- stles  -->
    <link href="./printNot.css" rel="stylesheet" type="text/css" media="all"/>
    <!-- for printing in records only  -->
    <link href="./print.css" rel="stylesheet" type="text/css" media="print"/>
    <!-- bootstrap  -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
</head>
<body>
<div id="p_body">
    <?php
        if (isset($_GET['emp_id']) && !empty($_GET['emp_id']) && isset($_GET['period_id']) && !empty($_GET['period_id'])) {
            $empId = intval($_GET['emp_id']);
            $periodId = intval($_GET['period_id']);

            // Fetch employee details
            $sql = "SELECT e.*, p.position FROM tbl_employee e 
                    JOIN tbl_positions p ON e.pos_id = p.pos_id 
                    WHERE e.emp_id = $empId";
            $employeeResult = mysqli_query($conn, $sql);

            // Fetch evaluation period
            $periodSql = "SELECT period FROM tbl_eval_period WHERE period_id = $periodId";
            $periodResult = mysqli_query($conn, $periodSql);
            $periodRow = mysqli_fetch_assoc($periodResult);
            $evaluationPeriod = $periodRow['period'];

            // Fetch evaluation details
            $evaluationSql = "SELECT * FROM tbl_evaluation 
                            WHERE emp_id = $empId AND period_id = $periodId";
            $evaluationResult = mysqli_query($conn, $evaluationSql); 

            $evaluations = [];
            $comments = [];
            $evaluatorCount = 0;
            $totalScore = 0;
            $scoreCount = 0;
            $evaluatorSums = [];
            $evaluatorCounts = [];

            while ($evaluationRow = mysqli_fetch_assoc($evaluationResult)) {
                $supCount = $evaluationRow['supi_count'];
                $evaluatorCount = max($evaluatorCount, $supCount);
                for ($i = 1; $i <= 13; $i++) {
                    $score = $evaluationRow['s' . $i];
                    $evaluations[$i][$supCount] = $score;
                    $totalScore += $score;
                    $scoreCount++;

                    if (!isset($evaluatorSums[$supCount])) {
                        $evaluatorSums[$supCount] = 0;
                        $evaluatorCounts[$supCount] = 0;
                    }
                    $evaluatorSums[$supCount] += $score;
                    $evaluatorCounts[$supCount]++;
                }
                $comments[$supCount] = [
                    'violation_comment' => $evaluationRow['violation_comment'],
                    'comment_recc' => $evaluationRow['comment_recc']
                ];
            }
            $averageScore = $scoreCount > 0 ? $totalScore / $scoreCount : 0;
            mysqli_close($conn);
        }
    ?>

    <div class="p_header">
        <img id="p_logo" src="../styles/GTRLOGO.png" alt="">
        <h5 class="text-center" id="p_title">EMPLOYEE'S PERFORMANCE EVALUATION</h5>
    </div>

    <table class="evaluation_table table table-bordered" border="1" width="100%" height="fit-content">
        <tr>
            <th colspan="4">EMPLOYEE'S NAME</th>
            <th colspan="6">EVALUATION PERIOD:</th>
            <th colspan="2">AVERAGE SCORE:</th>
        </tr>
        <tr>
            <?php 
            if ($row = mysqli_fetch_assoc($employeeResult)):
                echo '<th colspan="4" allign="center" class="_inp">'. htmlspecialchars($row['emp_lname'] . ', ' . $row['emp_fname'] . ' ' . $row['emp_minitial']).'. </th>';
                echo '<th colspan="6" class="_inp">'. htmlspecialchars($evaluationPeriod).' </th>'; // Display the evaluation period
                echo '<th colspan="2" rowspan="2" class="_ave"> <strong>'. number_format(($averageScore*10), 2).'</strong> </th>'; // Display the average score
            ?>
        </tr>
        <tr>
            <th>POSITION</th>
            <?php 
                echo '<th colspan="5" class="_inp">'. htmlspecialchars($row['position']).' </th>';
                echo '<th colspan="2"> DATE HIRED: </th>';
                $date = new DateTime($row['emp_date_hired']);
                $formatted_date = $date->format('m-d-Y');
                echo '<th colspan="2" class="_inp">'. htmlspecialchars($formatted_date).' </th>';
            endif;
            ?>
        </tr>
        <tr class="text-center">
            <th colspan="4" class="_det">Form No.: GTR-010</th>
            <th colspan="4" class="_det">Effective Date: May 10, 2023</th>
            <th colspan="4" class="_det">Rev. 00</th>
        </tr>
    </table>

    <p class="text-center pink">Bigyan ng iskor ang empleyado mula: <strong>1</strong> (Pinakamababa) hanggang <strong>10</strong> (Pinakamataas).</p>

    <table class="score_table table table-bordered" border="1" width="100%">
        <thead>
            <tr>
                <th> </th>
                <th colspan="6" class="text-center">EVALUATORS</th>
            </tr>
            <tr>
                <th> </th>
                <?php
                    for ($i = 1; $i <= 6; $i++) {
                        echo '<th class="_sum">' . $i . '</th>';
                    }
                ?>
            </tr>
        </thead>
        <!-- -------------------------body -->
        <tbody>
            <?php
                require('../questions.php');

                foreach ($questions_ as $qNum => $qText) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($qText) . '</td>';
                    for ($i = 1; $i <= 6; $i++) {
                        $score = isset($evaluations[$qNum][$i]) ? htmlspecialchars($evaluations[$qNum][$i]) : '';
                        echo '<td class="_score">' . $score . '</td>';
                    }
                    echo '</tr>';
                }

                // row for sums
                echo '<tr>';
                echo '<td class="text-end"><strong>Total</strong></td>';
                for ($i = 1; $i <= 6; $i++) {
                    $sum = isset($evaluatorSums[$i]) ? $evaluatorSums[$i] : 0;
                    echo '<td class="_sum"><strong>' . $sum. '</strong></td>';
                }
                echo '</tr>';

                // row for averages
                echo '<tr>';
                echo '<td class="text-end"><strong>Average </strong></td>';
                for ($i = 1; $i <= 6; $i++) {
                    $avg = isset($evaluatorCounts[$i]) && $evaluatorCounts[$i] > 0 ? $evaluatorSums[$i] / $evaluatorCounts[$i] : 0;
                    echo '<td class="_sum"><strong>' . number_format(($avg*10), 2) . '</strong></td>';
                }
                echo '</tr>';
            ?>

        </tbody>
    </table>         

    <div>
        <p class="border">PARA LAMANG SA HR/ADMIN OFFICER:</p>
    </div>

    <table class="comment_table table table-bordered">
        <thead>
            <tr>
                <th>I. Mga Violations o Paglabag ng Empleyado sa Company Policies at Memorandums</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th>
                    <?php
                        for ($i = 1; $i <= 6; $i++) {
                            if (isset($comments[$i]) && !empty($comments[$i]['violation_comment'])) {
                                echo '<p id="v_com"> • ' . htmlspecialchars($comments[$i]['violation_comment']) . '</p>';
                            }
                        }
                    ?>
                </th>
            </tr>
        </tbody>
        <thead>
            <tr>
                <th>II. Mga Komento at Rekomendasyon ng Supervisor/Evaluator para sa Empleyado</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th>
                    <?php
                        for ($i = 1; $i <= 6; $i++) {
                            if (isset($comments[$i]) && !empty($comments[$i]['comment_recc'])) {
                                echo '<p id="r_com"> • ' . htmlspecialchars($comments[$i]['comment_recc']) . '</p>';
                            }
                        }
                    ?>
                </th>
            </tr>
        </tbody>
        <thead>
            <tr>
                <th>III. Mga Komento at mga Pangako ng Empleyado</th>
            </tr>
        </thead>
        <tbody id="promise">
            <tr> 
                <th class="blank-cell-1"></th>
            </tr>
        </tbody>
    </table>

    <div>
        <table class="ack_table table table-bordered">
            <tr>
                <th class="w-50">Acknowledged and Received By:</th>
                <th class="w-50">Noted By:</th>
            </tr>
            <tr>
                <th class="blank-cell"></th>
                <th class="blank-cell"></th>
            </tr>
        </table>
    </div>
</div>

<?php
require('../template/footer.php');
?>
