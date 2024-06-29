<?php
require('./template/header.php');
?>
<style>
    .body_ {
        max-height: 100vh; /* Adjust this value as needed */
        overflow-y: auto;
        scrollbar-width: thin; /* Firefox */
        scrollbar-color: #595959 #f1f1f1; /* Firefox */
    }
    .body_::-webkit-scrollbar {
        width: 12px; /* For webkit browsers */
    }
    .score_input{
        border:3px solid #595959 !important;
        width: 80px;
        height: 60px !important;
        font-size: 15pt;
        margin-left: 25%;
    }
    #violation_comment,#comment_recc{
        border:3px solid #595959 !important;
        height: 150px;
    }
</style>
<?php
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
<div class="body_">

    <div class="container">
        <h2 class="lead fs-4 text-center">Add Evaluation <strong>[ <?php echo $supervisorCount?> ]</strong></h2>
        <p class="text-center" style="margin: 10px 0 10px 0 !important;">Bigyan ng iskor ang empleyado mula: <strong>1</strong> (Pinakamababa) hanggang <strong>10</strong> (Pinakamataas).</p>
        <form action="save_evaluation.php" method="POST">
            <input type="hidden" name="emp_id" value="<?php echo $empId; ?>">
            <input type="hidden" name="period_id" value="<?php echo $periodId; ?>">
            <input type="hidden" name="supervisor_count" value="<?php echo $supervisorCount; ?>">
            <hr>

            <!-- Display questions and input fields for scores -->
            <?php foreach ($questions as $qNum => $qText): ?>
                <div class="form-group mt-3 fs-3 fw-normal row align-items-center">
                    <div class="col-md-2 text-center">
                        <input type="number" class="form-control score_input small-input" placeholder="0" name="s<?php echo $qNum; ?>" id="s<?php echo $qNum; ?>" min="1" max="10" required>
                    </div>
                    <label for="s<?php echo $qNum; ?>" class="col-form-label col-md-10 text-md-right"><?php echo htmlspecialchars($qText); ?> (1-10)</label>
                </div>
                <hr>
            <?php endforeach; ?>

            <hr>
            <div class="form-group lead fw-semibold">
                <label for="violation_comment">Mga Violations o Paglabag ng Empleyado sa Company Policies at Memorandums</label>
                <textarea class="form-control" name="violation_comment" id="violation_comment"></textarea>
            </div>
                <hr>
            <div class="form-group lead fw-semibold">
                <label for="comment_recc">Mga Komento at Rekomendasyon ng Supervisor/Evaluator para sa Empleyado</label>
                <textarea class="form-control" name="comment_recc" id="comment_recc"></textarea>
            </div>
            <div class="text-end m-3">
                <p class="fs-6 fw-semibold text-danger ">Once <strong class="fs-5">SAVED</strong>, it can <strong class="fs-5">NO</strong> longer be edited.</p>
                <button type="submit" class="btn btn-outline-success btn-lg">Save Evaluation</button>
            </div>
            <br>
            
        </form>
    </div>

</div>

<?php
require('./template/footer.php');
mysqli_close($conn);
?>
