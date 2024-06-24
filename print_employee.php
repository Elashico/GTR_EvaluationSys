<?php
require('./template/header.php');
?>
<style>
    #p_body{
        margin: 0.5in;
        font-family: Calibri, serif;
    }
    #p_logo{
        width: 80px;
        height: auto;
        margin-right: 10px;
    }
    .p_header {
    display: flex;
    align-items: center; /* Aligns items vertically centered */
    justify-content: start; /* Centers the items horizontally */
    }
    #p_logo {
        margin-right: 0; /* Adds some space between the logo and the title */
    }

    #p_title {
        margin: 40px 0 0 85px;
        font-size: 20pt;
    }
</style>

<div id="p_body">

    <div class="p_header">
        <img id="p_logo" src="./styles/GTRLOGO.png" alt="">
        <h5 class="text-center" id="p_title">EMPLOYEE'S PERFORMANCE EVALUATION</h5>
    </div>

    <div class="detail_table">
    <table class="evaluation-table">
        <tr>
            <td class="label">EMPLOYEE'S NAME:</td>
            <td class="label">EVALUATION PERIOD:</td>
            <td class="label">AVERAGE SCORE:</td>
        </tr>
        <tr>
            <td class="value"></td> <!-- value for name  -->
            <td class="value">08/22/2012</td> <!-- value for period  -->
            <td class="value"></td> <!-- value for score  -->
        </tr>
        <tr>
            <td class="label">Form No.: GTR-010</td>
            <td class="value"></td>
            <td class="label">Effective Date:</td>
            <td class="value">May 10, 2023</td>
            <td class="label">Rev. 00</td>
            <td class="value"></td>
        </tr>
    </table>
    </div>

    <?php
    if (isset($_GET['emp_id']) && !empty($_GET['emp_id']) && isset($_GET['period_id']) && !empty($_GET['period_id'])) {
        $empId = intval($_GET['emp_id']);
        $periodId = intval($_GET['period_id']);

        // Fetch employee details
        $sql = "SELECT e.*, p.position FROM tbl_employee e 
                JOIN tbl_positions p ON e.pos_id = p.pos_id 
                WHERE e.emp_id = $empId";
        $employeeResult = mysqli_query($conn, $sql);

        // Fetch evaluation details
        $evaluationSql = "SELECT * FROM tbl_evaluation 
                        WHERE emp_id = $empId AND period_id = $periodId";
        $evaluationResult = mysqli_query($conn, $evaluationSql);

        if ($row = mysqli_fetch_assoc($employeeResult)) {
            echo '<p>Name: ' . htmlspecialchars($row['emp_lname'] . ', ' . $row['emp_fname'] . ' ' . $row['emp_minitial']) . '</p>';
            echo '<p>Position: ' . htmlspecialchars($row['position']) . '</p>';
            echo '<p>Date Hired: ' . htmlspecialchars($row['emp_date_hired']) . '</p>';
            echo '<hr>';

            // Process and display evaluation details
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
            echo '<table border="1">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Question</th>';
            for ($i = 1; $i <= 6; $i++) {
                echo '<th>Evaluator ' . $i . '</th>';
            }
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            require('./questions.php');

            foreach ($questions_ as $qNum => $qText) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($qText) . '</td>';
                for ($i = 1; $i <= 6; $i++) {
                    $score = isset($evaluations[$qNum][$i]) ? htmlspecialchars($evaluations[$qNum][$i]) : '';
                    echo '<td>' . $score . '</td>';
                }
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';

            echo'<div>';
                echo '<h3>Comments</h3>';
                echo '<div>';
                    echo '<h4>Violations:</h4>';
                    for ($i = 1; $i <= 6; $i++) {
                        if (isset($comments[$i])) {
                            echo '<p> • ' . htmlspecialchars($comments[$i]['violation_comment']) . '</p>';
                        } 
                    }
                echo '</div>';
                echo '<div>';
                    echo '<h4>Comments and Reccomendations</h4>';
                    for ($i = 1; $i <= 6; $i++) {
                        if (isset($comments[$i])) {
                            echo '<p> • ' . htmlspecialchars($comments[$i]['comment_recc']) . '</p>';
                        } 
                    }
                echo '</div>';
            echo'</div>';
            
        } else {
            echo 'Employee not found.';
        }

        mysqli_close($conn);
    } else {
        echo 'Invalid request.';
    }

    ?>
</div>

<?php
require('./template/footer.php');
?>
