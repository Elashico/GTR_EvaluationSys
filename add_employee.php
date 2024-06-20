<?php
require('./template/header.php');

// Fetch positions for the dropdown
$sql = "SELECT pos_id, position FROM tbl_positions";
$positionsResult = mysqli_query($conn, $sql);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $mname = mysqli_real_escape_string($conn, $_POST['mname']);
    $position = intval($_POST['position']);
    $date_hired = mysqli_real_escape_string($conn, $_POST['date_hired']);
    
    $sql = "INSERT INTO tbl_employee (emp_fname, emp_lname, emp_minitial, pos_id, emp_date_hired) VALUES ('$fname', '$lname', '$mname', $position, '$date_hired')";
    if (mysqli_query($conn, $sql)) {
        echo "New employee added successfully";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<div class="container">
    <h2 class="mt-3">Add New Employee</h2>
    <form action="" method="POST">
        <div class="form-group">
            <label for="fname">First Name</label>
            <input type="text" name="fname" class="form-control" id="fname" required>
        </div>
        <div class="form-group">
            <label for="lname">Last Name</label>
            <input type="text" name="lname" class="form-control" id="lname" required>
        </div>
        <div class="form-group">
            <label for="mname">Middle Initial</label>
            <input type="text" name="mname" class="form-control" id="mname">
        </div>
        <div class="form-group">
            <label for="position">Position</label>
            <select name="position" class="form-control" id="position" required>
                <option value="">Select Position</option>
                <?php
                // Populate the dropdown with positions from the database
                if (mysqli_num_rows($positionsResult) > 0) {
                    while ($row = mysqli_fetch_assoc($positionsResult)) {
                        echo '<option value="' . htmlspecialchars($row['pos_id']) . '">' . htmlspecialchars($row['position']) . '</option>';
                    }
                } else {
                    echo '<option value="">No positions found</option>';
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="date_hired">Date Hired</label>
            <input type="date" name="date_hired" class="form-control" id="date_hired" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Employee</button>
    </form>
</div>

<?php
require('./template/footer.php');
?>
