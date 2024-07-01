<?php
require('./template/header.php');

if (isset($_GET['emp_id']) && isset($_GET['period_id'])) {
    $empId = intval($_GET['emp_id']);
    $periodId = intval($_GET['period_id']);

    $evaluationSql = "SELECT * FROM tbl_evaluation 
                    WHERE emp_id = $empId AND period_id = $periodId";
    $evaluationResult = mysqli_query($conn, $evaluationSql);

    $evaluations = [];
    $comments = [];
    $evaluatorCount = 0;

    while ($evaluationRow = mysqli_fetch_assoc($evaluationResult)) {
        $supCount = $evaluationRow['supi_count'];
        $evaluatorCount = max($evaluatorCount, $supCount);
        for ($i = 1; $i <= 13; $i++) {
            $evaluations[$i][$supCount] = $evaluationRow['s' . $i];
        }
        $comments[$supCount] = [
            'violation_comment' => $evaluationRow['violation_comment'],
            'comment_recc' => $evaluationRow['comment_recc']
        ];
    }
    echo '<h1 class="lead ms-3 mb-3 fw-medium fs-4">Evaluation</h1>';
    echo '<div class="table-responsive">';
    echo '<table class="table table-bordered">';
    echo '<thead class="thead-light">';
        echo '<tr>';
            echo '<th scope="col" class="lead fs-3 text-center"></th>';
            echo '<th scope="col" colspan="6" class="lead text-center fs-6 fw-semibold">Evaluator</th>';
        echo '</tr>';
        echo '<tr>';
            echo '<th scope="col" class="lead fs-3 text-center"></th>';
            for ($i = 1; $i <= 6; $i++) {
                echo '<th scope="col" class="lead text-center fs-6 fw-semibold">' . $i . '</th>';
            }
        echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    require('./questions.php');

    $evaluatorSums = array_fill(1, 6, 0);
    $evaluatorCounts = array_fill(1, 6, 0);

    foreach ($questions as $qNum => $qText) {
        echo '<tr>';
        echo '<th scope="row" class="lead fs-6 fw-normal">' . htmlspecialchars($qText) . '</th>';

        for ($i = 1; $i <= 6; $i++) {
            $score = isset($evaluations[$qNum][$i]) ? htmlspecialchars($evaluations[$qNum][$i]) : '';
            if (!empty($score)) {
                $evaluatorSums[$i] += intval($score);
                $evaluatorCounts[$i]++;
            }
            echo '<td class="text-center fs-5 text-danger">' . $score . '</td>';
        }

        echo '</tr>';
    }

    echo '<tr>';
    echo '<th scope="row" class="fs-5 fw-semibold text-end">Total</th>';
    foreach ($evaluatorSums as $sum) {
        echo '<td class="text-center fs-5 fw-bold text-success-emphasis">' . $sum . '</td>';
    }
    echo '</tr>';

    echo '<tr>';
    echo '<th scope="row" class="fs-5 fw-semibold text-end" >Average</th>';
    foreach ($evaluatorSums as $i => $sum) {
        if ($evaluatorCounts[$i] > 0) {
            $average = $sum / $evaluatorCounts[$i] * 10;
            echo '<td class="text-center fs-5 fw-bold text-success-emphasis">' . number_format($average, 2) . '</td>';
        } else {
            echo '<td></td>';
        }
    }
    echo '</tr>';

    echo '</tbody>';
    echo '</table>';
    echo '</div>';

        // Compute the final average score
        $totalAverageSum = 0;
        $numberOfEvaluators = 0;
        foreach ($evaluatorSums as $i => $sum) {
            if ($evaluatorCounts[$i] > 0) {
                $totalAverageSum += $sum / $evaluatorCounts[$i];
                $numberOfEvaluators++;
            }
        }
        $finalAverageScore = ($numberOfEvaluators > 0) ? ($totalAverageSum / $numberOfEvaluators) * 10 : 0;
        
        // Display the final average score
        echo '<div class="text-end">';
        echo '<h3 class="fs-3">Average Score: <strong class="fs-2">' . number_format($finalAverageScore, 2) . '</strong></h3>';
        echo '</div>';
        echo '<hr>';


        echo '<div>';
            echo '<section class="text-center">';
                echo '<h3 class="mb-3 fs-4">Comments</h3>';
            echo '</section>';
        for ($i = 1; $i <= 6; $i++) {
            if (isset($comments[$i])) {
                echo '<h4 class="lead fw-semibold">Evaluator ' . $i . '</h4>';
                echo '<ul>';
                if (!empty($comments[$i]['violation_comment'])) {
                    echo '<li>Violations: ' . htmlspecialchars($comments[$i]['violation_comment']) . '</li>';
                }
                if (!empty($comments[$i]['comment_recc'])) {
                    echo '<li>Comment: ' . htmlspecialchars($comments[$i]['comment_recc']) . '</li>';
                }
                echo '</ul>';
            } else {
                echo '<h4 class="lead fw-semibold">Evaluator ' . $i . '</h4>';
                echo '<p class="text-secondary">Have not evaluated.</p>';
            }
        }
        echo '</div>';
        echo '<hr>';

        echo '<div class="my-3 text-end">';
        if ($evaluatorCount < 6) {
            echo '<button type="button" class="btn btn-outline-success btn-lg" onclick="openEvaluationWindow()">Add Evaluation</button>';
        } else {
            echo '<button type="button" class="btn btn-outline-success btn-lg" disabled>Max Evaluations Reached</button>';
        }
        echo '<button type="button" class="btn btn-outline-dark btn-lg ms-4" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Print" onclick="openPrintWindow()">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer-fill" viewBox="0 0 16 16">
                <path d="M5 1a2 2 0 0 0-2 2v1h10V3a2 2 0 0 0-2-2zm6 8H5a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1"/>
                <path d="M0 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-1v-2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2H2a2 2 0 0 1-2-2zm2.5 1a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/>
            </svg></button>';
        echo '</div>';
    }
?>
<div></div>
