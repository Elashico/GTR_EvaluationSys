<?php
    require('./template/header.php');

?>

<div class="container">
    <div class="row">
        <div class="col-md-3">
            <?php require('./empsearch.php'); ?>
        </div>
        <div class="col-md-8" id="employeeRecords">
            <?php
            if (isset($_GET['emp_id']) && !empty($_GET['emp_id'])) {
                $empId = intval($_GET['emp_id']);

                $sql = "SELECT e.*, p.position FROM tbl_employee e 
                        JOIN tbl_positions p ON e.pos_id = p.pos_id 
                        WHERE e.emp_id = $empId";
                $employeeResult = mysqli_query($conn, $sql);

                if ($row = mysqli_fetch_assoc($employeeResult)) {
                    echo '<h2>Employee Details</h2>';
                    echo '<p>Name: ' . htmlspecialchars($row['emp_lname'] . ', ' . $row['emp_fname'] . ' ' . $row['emp_minitial']) . '.</p>';
                    echo '<p>Position: ' . htmlspecialchars($row['position']) . '</p>';
                    echo '<p>Date Hired: ' . htmlspecialchars($row['emp_date_hired']) . '</p>';
                } else {
                    echo 'Employee not found.';
                }

                $periodSql = "SELECT period_id, period FROM tbl_eval_period";
                $periodsResult = mysqli_query($conn, $periodSql);

                echo '<form action="" method="GET">';
                echo '<input type="hidden" name="emp_id" value="' . $empId . '">';
                echo '<div class="form-group">';
                echo '<label for="period_id">Select Evaluation Period:</label>';
                echo '<select class="form-control" name="period_id" id="period_id" onchange="this.form.submit()">';
                echo '<option value="">Select Evaluation Period</option>';

                if (mysqli_num_rows($periodsResult) > 0) {
                    while ($periodRow = mysqli_fetch_assoc($periodsResult)) {
                        $selected = isset($_GET['period_id']) && $_GET['period_id'] == $periodRow['period_id'] ? ' selected' : '';
                        echo '<option value="' . htmlspecialchars($periodRow['period_id']) . '"' . $selected . '>' . htmlspecialchars($periodRow['period']) . '</option>';
                    }
                } else {
                    echo '<option value="">No periods found</option>';
                }

                echo '</select>';
                echo '</div>';
                echo '</form>';

                if (isset($_GET['period_id']) && !empty($_GET['period_id'])) {
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

                    echo '<h2>Evaluation Details</h2>';
                    echo '<div class="table-responsive">';
                    echo '<table class="table table-bordered">';
                    echo '<thead class="thead-light">';
                    echo '<tr>';
                    echo '<th scope="col">Question</th>';
                    for ($i = 1; $i <= 6; $i++) {
                        echo '<th scope="col">Evaluator ' . $i . '</th>';
                    }
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';

                    $questions = [
                        1 => 'Eksakto at maayos na pagsunod sa mga proseso…',
                        2 => 'Alam ang mga proseso at standards ng kanyang…',
                        3 => 'Propesyonal na pakikitungo at may respeto…',
                        4 => 'Bukas ang isipan at may kakayahang  matuto…',
                        5 => 'Hindi umaabsent/lumiliban sa trabaho ng walang…',
                        6 => 'Kinakabisado, isinasapuso at sinusunod ang mga…',
                        7 => 'Sinisiguradong ligtas ang area na pinagtatrabahuan…',
                        8 => 'Malinis ang pangangatawan at tama ang gupit at…',
                        9 => 'Mabilis gumalaw, maaasahan at nagkukusang…',
                        10 => 'Nililinis at inaayos ang mga kagamitang ginagamit…',
                        11 => 'Mabilis matuto at may kakayahan ding ituro ang…',
                        12 => 'Madami ang natatapos na trabaho sa loob ng duty…',
                        13 => 'Pinipili ang mga trabaho na dapat unahin at…'
                    ];

                    $evaluatorSums = array_fill(1, 6, 0);
                    $evaluatorCounts = array_fill(1, 6, 0);

                    foreach ($questions as $qNum => $qText) {
                        echo '<tr>';
                        echo '<th scope="row">' . htmlspecialchars($qText) . '</th>';

                        for ($i = 1; $i <= 6; $i++) {
                            $score = isset($evaluations[$qNum][$i]) ? htmlspecialchars($evaluations[$qNum][$i]) : '';
                            if (!empty($score)) {
                                $evaluatorSums[$i] += intval($score);
                                $evaluatorCounts[$i]++;
                            }
                            echo '<td>' . $score . '</td>';
                        }

                        echo '</tr>';
                    }

                    echo '<tr>';
                    echo '<th scope="row">Sum</th>';
                    foreach ($evaluatorSums as $sum) {
                        echo '<td>' . $sum . '</td>';
                    }
                    echo '</tr>';

                    echo '<tr>';
                    echo '<th scope="row">Average</th>';
                    foreach ($evaluatorSums as $i => $sum) {
                        if ($evaluatorCounts[$i] > 0) {
                            $average = $sum / $evaluatorCounts[$i] * 10;
                            echo '<td>' . number_format($average, 2) . '</td>';
                        } else {
                            echo '<td></td>';
                        }
                    }
                    echo '</tr>';

                    echo '</tbody>';
                    echo '</table>';
                    echo '</div>';

                    echo '<h3>Comments</h3>';
                    for ($i = 1; $i <= 6; $i++) {
                        if (isset($comments[$i])) {
                            echo '<h4>Evaluator ' . $i . '</h4>';
                            echo '<ul>';
                            if (!empty($comments[$i]['violation_comment'])) {
                                echo '<li>Violation Comment: ' . htmlspecialchars($comments[$i]['violation_comment']) . '</li>';
                            }
                            if (!empty($comments[$i]['comment_recc'])) {
                                echo '<li>Recommendation Comment: ' . htmlspecialchars($comments[$i]['comment_recc']) . '</li>';
                            }
                            echo '</ul>';
                        } else {
                            echo '<h4>Evaluator ' . $i . '</h4>';
                            echo '<p>No comments provided.</p>';
                        }
                    }

                    if ($evaluatorCount < 6) {
                        echo '<button type="button" class="btn btn-primary" onclick="openEvaluationWindow()">Add Evaluation</button>';
                    } else {
                        echo '<button type="button" class="btn btn-primary" disabled>Max Evaluations Reached</button>';
                    }
                }
            } else {
                echo 'Select an employee to view details.';
            }

            mysqli_close($conn);
            ?>
        </div>
    </div>
</div>

<script>
function openEvaluationWindow() {
    const empId = <?php echo isset($empId) ? $empId : 'null'; ?>;
    const periodId = <?php echo isset($periodId) ? $periodId : 'null'; ?>;
    if (empId && periodId) {
        window.open(`./add_evaluation.php?emp_id=${empId}&period_id=${periodId}`, 'Add Evaluation', 'width=600,height=400');
    }
}
</script>

<?php
    require('./template/footer.php');
?>
