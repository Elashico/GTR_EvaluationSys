<?php
require('./template/header.php');

// Ensure emp_id and period_id are provided
$empId = isset($_GET['emp_id']) ? intval($_GET['emp_id']) : null;
$periodId = isset($_GET['period_id']) ? intval($_GET['period_id']) : null;

if ($empId === null || $periodId === null) {
    die('Invalid parameters.');
}

// Retrieve the last evaluation count
$evaluationSql = "SELECT MAX(supi_count) AS last_sup_count FROM tbl_evaluation WHERE emp_id = $empId AND period_id = $periodId";
$evaluationResult = mysqli_query($conn, $evaluationSql);
$lastSupCount = 0;

if ($evaluationResult && mysqli_num_rows($evaluationResult) > 0) {
    $row = mysqli_fetch_assoc($evaluationResult);
    $lastSupCount = intval($row['last_sup_count']);
}

// Calculate the new supervisor count
$supervisorCount = $lastSupCount + 1;

// Array of questions
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

?>

<div class="container">
    <h2>Add Evaluation</h2>
    <h3>Evaluator <?php echo $supervisorCount?></h3>
    <form action="save_evaluation.php" method="POST">
        <input type="hidden" name="emp_id" value="<?php echo $empId; ?>">
        <input type="hidden" name="period_id" value="<?php echo $periodId; ?>">
        <input type="hidden" name="supervisor_count" value="<?php echo $supervisorCount; ?>">

        <!-- Display questions and input fields for scores -->
        <?php foreach ($questions as $qNum => $qText): ?>
            <div class="form-group">
                <label for="s<?php echo $qNum; ?>"><?php echo htmlspecialchars($qText); ?>:</label>
                <input type="number" class="form-control" name="s<?php echo $qNum; ?>" id="s<?php echo $qNum; ?>" required>
            </div>
        <?php endforeach; ?>

        <div class="form-group">
            <label for="violation_comment">Violation Comment:</label>
            <textarea class="form-control" name="violation_comment" id="violation_comment"></textarea>
        </div>

        <div class="form-group">
            <label for="comment_recc">Recommendation Comment:</label>
            <textarea class="form-control" name="comment_recc" id="comment_recc"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Save Evaluation</button>
    </form>
</div>

<?php
require('./template/footer.php');
mysqli_close($conn);
?>
