<?php
    require('./template/header.php');
?>

<div class="">
    <div class="row">
        <div class="col-md-3 side_navigation sticky-sm-top">
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
                    echo '<h1 class="lead mt-3 fw-medium fs-4">Employee Details</h1>';
                    echo '<hr>';
                    echo '<p class="lead text-capitalize fs-5"> Name: <strong>' . htmlspecialchars($row['emp_lname'] . ', ' . $row['emp_fname'] . ' ' . $row['emp_minitial']) . '.</strong></p>';
                    echo '<p class="lead fs-5" > Position: <strong>' . htmlspecialchars($row['position']) . '</strong></p>';
                    echo '<p class="lead fs-5"> Date Hired: <strong>' . htmlspecialchars($row['emp_date_hired']) . '</strong></p>';
                } else {
                    echo 'Employee not found.';
                }

                $periodSql = "SELECT period_id, period FROM tbl_eval_period";
                $periodsResult = mysqli_query($conn, $periodSql);

                echo '<form action="" method="GET">';
                echo '<input type="hidden" name="emp_id" value="' . $empId . '">';
                echo '<div class="input-group mb-3">';
                echo '<label for="period_id" class="input-group-text lead" >Evaluation Period</label>';
                echo '<select class="form-select" name="period_id" id="period_id" onchange="this.form.submit()">';
                echo '<option value="">select evaluation period</option>';

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
                echo '<hr class="my-4">';

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

                    echo '<h1 class="lead mb-3 fw-medium fs-4">Evaluation</h1>';
                    echo '<div class="table-responsive">';
                    echo '<table class="table table-bordered">';
                    echo '<thead class="thead-light">';
                    echo '<tr>';
                    echo '<th scope="col" class="lead fs-3 text-center">Question</th>';
                    for ($i = 1; $i <= 6; $i++) {
                        echo '<th scope="col" class="lead text-center fs-6 fw-semibold">Evaluator ' . $i . '</th>';
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
                    echo '<th scope="row" class="fs-5 fw-semibold text-center">Sum</th>';
                    foreach ($evaluatorSums as $sum) {
                        echo '<td class="text-center fs-5 fw-bold text-success-emphasis">' . $sum . '</td>';
                    }
                    echo '</tr>';

                    echo '<tr>';
                    echo '<th scope="row" class="fs-5 fw-semibold text-center" >Average</th>';
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
                    echo '<h3 class="fs-3">Final Score: <strong class="fs-2">' . number_format($finalAverageScore, 2) . '</strong></h3>';
                    echo '</div>';
                    echo '<hr>';


                    echo '<div>';
                        echo '<section class="text-center">';
                            echo '<h3 class="mb-3">Comments</h3>';
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
                            echo '<p class="text-secondary">No comments provided.</p>';
                        }
                    }

                    echo '</div>';
                    echo '<hr>';

                    echo '<div class="mb-3 text-end">';
                    if ($evaluatorCount < 6) {
                        echo '<button type="button" class="btn btn-outline-danger btn-lg" onclick="openEvaluationWindow()">Add Evaluation</button>';
                    } else {
                        echo '<button type="button" class="btn btn-outline-danger btn-lg" disabled>Max Evaluations Reached</button>';
                    }
                        echo '<button type="button" class="btn btn-outline-secondary ms-4 btn-lg" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Print" onclick="openPrintWindow()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer-fill" viewBox="0 0 16 16">
                            <path d="M5 1a2 2 0 0 0-2 2v1h10V3a2 2 0 0 0-2-2zm6 8H5a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1"/>
                            <path d="M0 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-1v-2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2H2a2 2 0 0 1-2-2zm2.5 1a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/>
                        </svg></button>';
                     echo '</div>';

                        
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
        window.open(`./add_evaluation.php?emp_id=${empId}&period_id=${periodId}`, 'Add Evaluation', 'width=900,height=500');
    }
}
function openPrintWindow() {
    const empId = <?php echo isset($empId) ? $empId : 'null'; ?>;
    const periodId = <?php echo isset($periodId) ? $periodId : 'null'; ?>;
    if (empId && periodId) {
        window.open(`./print_employee.php?emp_id=${empId}&period_id=${periodId}`, 'Print Employee', 'width=900,height=500');
    }
}
</script>

<?php
    require('./template/footer.php');
?>
