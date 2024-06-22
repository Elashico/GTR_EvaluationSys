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
                    echo '<h1 class="display-5 mt-3 fw-medium">Employee Details</h1>';
                    echo '<hr>';
                    echo '<p class="lead text-capitalize"> Name: ' . htmlspecialchars($row['emp_lname'] . ', ' . $row['emp_fname'] . ' ' . $row['emp_minitial']) . '.</p>';
                    echo '<p class="lead"> Position: ' . htmlspecialchars($row['position']) . '</p>';
                    echo '<p class="lead"> Date Hired: ' . htmlspecialchars($row['emp_date_hired']) . '</p>';
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

                    echo '<h2>Evaluation Details</h2>';
                    echo '<div class="table-responsive">';
                    echo '<table class="table table-bordered">';
                    echo '<thead class="thead-light">';
                    echo '<tr>';
                    echo '<th scope="col" class="display-6 text-center">Question</th>';
                    for ($i = 1; $i <= 6; $i++) {
                        echo '<th scope="col" class="lead text-center fs-6 fw-semibold">Evaluator ' . $i . '</th>';
                    }
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';

                    $questions = [
                        1 => 'Eksakto at maayos na pagsunod sa mga proseso at standards ng trabaho na naaayon sa pangangailangan ng Supervisor at Management.',
                        2 => 'Alam ang mga proseso at standards ng kanyang trabaho',
                        3 => 'Propesyonal na pakikitungo at may respeto sa mga supervisor, kapwa empleyado at maging sa customers.',
                        4 => 'Bukas ang isipan at may kakayahang  matuto sa tuing pinagsasabihan, itinatama, at tinuturuan ng mga supervisor at ibang kasamahan sa trabaho; Hindi agad pinapairal ang emosyon kapag pinagsasabihan, binibigyang payo at hindi pala-reklamo.',
                        5 => 'Hindi umaabsent/lumiliban sa trabaho ng walang paalam at hindi nalelate. Dumarating sa trabaho ng nakahanda at sinusunod ang mga reminders, announcements, memorandum at iba pang palatuntunin ng supervisors at ng kompanya.',
                        6 => 'Kinakabisado, isinasapuso at sinusunod ang mga reminders, announcements, memorandum at iba pang palatuntunin ng supervisors at ng kompanya.',
                        7 => 'Sinisiguradong ligtas ang area na pinagtatrabahuan. Umiiwas sa aksidene at ma-ingat na ginagawa ang trabaho.',
                        8 => 'Malinis ang pangangatawan at tama ang gupit at at pag-ahit. Sinusuot ang tamang uniporme at presentable',
                        9 => 'Mabilis gumalaw, maasahan at nagkukusang tumulong sa mga katrabaho sa mga oras na hindi busy.',
                        10 => 'Nililinis at inaayos ang mga kagamiting ginagamit pati na din ang kanyang work area.',
                        11 => 'Mabilis matuto at may kakayahan ding ituro ang mga gawain sa iba.',
                        12 => 'Madami ang natatapos na trabaho sa loob ng duty at handa ring sundin ang ipinag-uutos ng mga supervisor at ng mga nakakataas.',
                        13 => 'Pinipili ang mga trabaho na dapat unahin at handing magtrabaho nang lampas sa nakatakdang oras.'
                    ];

                    $evaluatorSums = array_fill(1, 6, 0);
                    $evaluatorCounts = array_fill(1, 6, 0);

                    foreach ($questions as $qNum => $qText) {
                        echo '<tr>';
                        echo '<th scope="row" class="lead fs-6">' . htmlspecialchars($qText) . '</th>';

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
                    echo '<div>';
                    echo '<h3>Final Average Score: ' . number_format($finalAverageScore, 2) . '</h3>';
                    echo '</div>';
                    echo '<hr>';

                    echo '<h3 class="mb-3">Comments</h3>';
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
                            echo '<p>No comments provided.</p>';
                        }
                    }

                    echo '<hr>';

                    if ($evaluatorCount < 6) {
                        echo '<button type="button" class="btn btn-outline-danger mb-3 btn-lg" onclick="openEvaluationWindow()">Add Evaluation</button>';
                    } else {
                        echo '<button type="button" class="btn btn-outline-danger mb-3 btn-lg" disabled>Max Evaluations Reached</button>';
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
        window.open(`./add_evaluation.php?emp_id=${empId}&period_id=${periodId}`, 'Add Evaluation', 'width=900,height=500');
    }
}
</script>

<?php
    require('./template/footer.php');
?>
