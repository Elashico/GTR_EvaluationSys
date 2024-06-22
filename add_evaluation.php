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

?>

<div class="container">
    <h2 class="display-3 text-center">Add Evaluation</h2>
    <h2 class="display-6 text-center">You are evaluator <?php echo $supervisorCount?></h2>
    <form action="save_evaluation.php" method="POST">
        <input type="hidden" name="emp_id" value="<?php echo $empId; ?>">
        <input type="hidden" name="period_id" value="<?php echo $periodId; ?>">
        <input type="hidden" name="supervisor_count" value="<?php echo $supervisorCount; ?>">

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
        <div class="text-center">
            <button type="submit" class="btn btn-outline-success m-3">Save Evaluation</button>
        </div>
        
    </form>
</div>

<?php
require('./template/footer.php');
mysqli_close($conn);
?>
