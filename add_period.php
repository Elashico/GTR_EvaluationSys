<?php
require('./template/header.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $period = mysqli_real_escape_string($conn, $_POST['period']);
    $year = mysqli_real_escape_string($conn, $_POST['year']);
    
    $periodString = $period . " " . $year;

    $sql = "INSERT INTO tbl_eval_period (period) VALUES ('$periodString')";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('New period added successfully'); window.close();</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<div class="container">
    <h2 class="mt-3">Add New Evaluation Period</h2>
    <hr>
    <form action="" method="POST">
        <div class="form-group">
            <label for="period">Period</label>
            <select name="period" class="form-control" id="period" required>
                <option value="Jan - May">Jan - May</option>
                <option value="June - Dec">June - Dec</option>
            </select>
        </div>
        <div class="mt-3 form-group">
            <label for="year">Year</label>
            <input type="number" name="year" class="form-control" id="year" min="2024" max="2100" required>
        </div>
        <div class="text-center mt-3">
            <button type="submit" class="btn btn-outline-primary">Add Period</button>
        </div>
    </form>
</div>

<?php
require('./template/footer.php');
?>
