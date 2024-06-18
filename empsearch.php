<?php
// Database connection

// Fetch positions for the dropdown
$sql = "SELECT pos_id, position FROM tbl_positions";
$positionsResult = mysqli_query($conn, $sql);
?>
<!-- Side Nav -->
<div>
    <div>
        <h1 class="mt-3">Find Employee</h1>
    </div>

    <div>
        <form action="" method="GET">
            <select name="position" onchange="this.form.submit()">
                <option value="">Select Position</option>
                <?php
                // Populate the dropdown with positions from the database
                if (mysqli_num_rows($positionsResult) > 0) {
                    while ($row = mysqli_fetch_assoc($positionsResult)) {
                        $selected = isset($_GET['position']) && $_GET['position'] == $row['pos_id'] ? ' selected' : '';
                        echo '<option value="' . htmlspecialchars($row['pos_id']) . '"' . $selected . '>' . htmlspecialchars($row['position']) . '</option>';
                    }
                } else {
                    echo '<option value="">No positions found</option>';
                }
                ?>
            </select>
        </form>
    </div>

    <div>
        <form action="" method="GET">
            <input type="hidden" name="position" value="<?php echo isset($_GET['position']) ? htmlspecialchars($_GET['position']) : ''; ?>">
            <input type="text" name="search" placeholder="Search employee..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <?php
    // Check if a position is selected or a search term is provided, and fetch the corresponding employees
    $positionFilter = isset($_GET['position']) && !empty($_GET['position']) ? 'AND pos_id = ' . intval($_GET['position']) : '';
    $searchFilter = isset($_GET['search']) && !empty($_GET['search']) ? "AND (emp_fname LIKE '%" . mysqli_real_escape_string($conn, $_GET['search']) . "%' OR emp_lname LIKE '%" . mysqli_real_escape_string($conn, $_GET['search']) . "%')" : '';

    $sql = "SELECT emp_id, emp_fname, emp_lname FROM tbl_employee WHERE 1=1 $positionFilter $searchFilter";
    $employeesResult = mysqli_query($conn, $sql);

    echo '<div id="employeeList"><ul>';
    if (mysqli_num_rows($employeesResult) > 0) {
        while ($row = mysqli_fetch_assoc($employeesResult)) {
            $emp_id = htmlspecialchars($row['emp_id']);
            $emp_name = htmlspecialchars($row['emp_fname'] . ' ' . $row['emp_lname']);
            echo "<li><a href=\"emprecord.php?emp_id=$emp_id\">$emp_name</a></li>";
        }
    } else {
        echo 'No employees found.';
    }
    echo '</ul></div>';
    ?>
</div>
