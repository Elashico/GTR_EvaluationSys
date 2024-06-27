<?php
require('./template/header.php');

// Function to delete evaluation period
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_period'])) {
    $period_id = intval($_POST['delete_period']);

    $sql = "DELETE FROM tbl_eval_period WHERE period_id = $period_id";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Evaluation period deleted successfully'); window.opener.location.reload();window.close();</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_period'])) {
    $period = mysqli_real_escape_string($conn, $_POST['period']);
    $year = mysqli_real_escape_string($conn, $_POST['year']);
    
    $periodString = $period . " " . $year;

    $sql = "INSERT INTO tbl_eval_period (period) VALUES ('$periodString')";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('New period added successfully'); window.opener.location.reload();window.close();</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<div class="container">
    <h2 class="mt-3">Manage Evaluation Periods</h2>
    <hr>
    <h3 class="mt-3">Add an Evaluation Period</h3>
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
        <div class="text-end mt-3">
            <button type="submit" name="add_period" class="btn btn-outline-primary">Add Period</button>
        </div>
    </form>
    <hr>
    <!-- Form to delete evaluation period -->
    <h3 class="mt-3">Delete an Evaluation Period</h3>
    <form action="" method="POST">
        <div class="form-group">
            <select name="delete_period" class="form-control" id="delete_period" required>
                <option value="">select period to delete</option>
                <?php
                $sql = "SELECT * FROM tbl_eval_period ORDER BY period_id DESC";
                $result = mysqli_query($conn, $sql);
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<option value="' . $row['period_id'] . '">' . htmlspecialchars($row['period']) . '</option>';
                    }
                } else {
                    echo '<option disabled>No periods found</option>';
                }
                ?>
            </select>
        </div>
        <div class="text-end mt-3">
            <label class="fs-6 fw-semibold text-danger">**This action will also remove all records associated with that period.**</>
            <button type="submit" class="btn btn-outline-danger">Delete Period</button>
        </div>
    </form>
</div>

<?php
require('./template/footer.php');
?>
