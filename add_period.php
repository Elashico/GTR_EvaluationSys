<?php
require('./template/header.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $period = mysqli_real_escape_string($conn, $_POST['period']);
    $year = mysqli_real_escape_string($conn, $_POST['year']);
    
    $periodString = $period . " " . $year;

    $sql = "INSERT INTO tbl_eval_period (period) VALUES ('$periodString')";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('New evaluation period added successfully'); window.close();</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<div class="container">
    <h2 class="mt-3">Add New Evaluation Period</h2>
    <form action="" method="POST">
        <div class="form-group">
            <label for="period">Period</label>
            <select name="period" class="form-control" id="period" required>
                <option value="Jan - May">Jan - May</option>
                <option value="June - Dec">June - Dec</option>
            </select>
        </div>
        <div class="form-group">
            <label for="year">Year</label>
            <input type="number" name="year" class="form-control" id="year" min="2024" max="2100" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Period</button>
    </form>
</div>

<?php
require('./template/footer.php');
?>
