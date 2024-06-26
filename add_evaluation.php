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
require('./questions.php');

?>

<div class="container">
    <h2 class="lead fs-4 text-center">Add Evaluation</h2>
    <h2 class="display-6 text-center">You are evaluator <?php echo $supervisorCount?></h2>
    <form action="save_evaluation.php" method="POST">
        <input type="hidden" name="emp_id" value="<?php echo $empId; ?>">
        <input type="hidden" name="period_id" value="<?php echo $periodId; ?>">
        <input type="hidden" name="supervisor_count" value="<?php echo $supervisorCount; ?>">
        <hr>

        <!-- Display questions and input fields for scores -->
        <?php foreach ($questions as $qNum => $qText): ?>
            <div class="form-group mt-3 fs-5 fw-semibold">
                <label for="s<?php echo $qNum; ?>"><?php echo htmlspecialchars($qText); ?> (1-10)</label>
                <input type="number" class="form-control" name="s<?php echo $qNum; ?>" id="s<?php echo $qNum; ?>" min="1" max="10" required>
            </div>
        <?php endforeach; ?>
        <hr>
        <div class="form-group lead fw-semibold">
            <label for="violation_comment">Violation Comment:</label>
            <textarea class="form-control" name="violation_comment" id="violation_comment"></textarea>
        </div>
            <hr>
        <div class="form-group lead fw-semibold">
            <label for="comment_recc">Recommendation Comment:</label>
            <textarea class="form-control" name="comment_recc" id="comment_recc"></textarea>
        </div>
        <div class="text-center m-3">
            <p class="fs-6 fw-semibold text-danger ">Once <strong class="fs-5">SAVED</strong>, it can <strong class="fs-5">NO</strong> longer be edited.</p>
            <button type="submit" class="btn btn-outline-success btn-lg">Save Evaluation</button>
        </div>
        
    </form>
</div>

<?php
require('./template/footer.php');
mysqli_close($conn);
?>
