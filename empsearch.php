<?php
// Database connection

// Fetch positions for the dropdown
$sql = "SELECT pos_id, position FROM tbl_positions";
$positionsResult = mysqli_query($conn, $sql);
?>
<!-- Side Nav -->
<div class="side_navigation container-fluid">
    <div class="text-center">
        <h1 class="mt-3 fs-5">Find Employee</h1>
        <hr>
    </div>

    <div class="">
        <div class="form-floating">
                <form action="" method="GET">
                    <select name="position" class="form-select" onchange="this.form.submit()">
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

            <div class="mt-3">
                <form action="" method="GET" class="d-flex">
                    <input type="hidden" name="position" value="<?php echo isset($_GET['position']) ? htmlspecialchars($_GET['position']) : ''; ?>">
                    <input type="search" name="search" class="form-control" style="width: 75%;" placeholder="Search employee..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit" class="btn btn-outline-secondary" style="width: 23%; text-align:center; margin-left: 2%;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                            </svg>
                    </button>
                </form>
            </div>
    </div>

    <div class="container-fluid text-center mt-3">
        <!-- New Employee -->
        <button type="button" class="nSid btn btn-outline-secondary" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Add New Employee" onclick="openNewEmployeeWindow()">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="16" fill="currentColor" class="bi bi-person-fill-add" viewBox="0 0 16 16">
            <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0m-2-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
            <path d="M2 13c0 1 1 1 1 1h5.256A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1 1.544-3.393Q8.844 9.002 8 9c-5 0-6 3-6 4"/>
        </svg>
        </button>
        <!-- New Position -->
        <button type="button" class="nSid btn btn-outline-secondary" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Edit Positions" onclick="openNewPositionWindow()">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-badge-fill" viewBox="0 0 16 16">
            <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2zm4.5 0a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zM8 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6m5 2.755C12.146 12.825 10.623 12 8 12s-4.146.826-5 1.755V14a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1z"/>
        </svg>
        </button>
        <!-- New Period -->
        <button type="button" class="nSid btn btn-outline-secondary" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Add Evaluation Period" onclick="openNewPeriodWindow()">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar-plus" viewBox="0 0 16 16">
            <path d="M8 7a.5.5 0 0 1 .5.5V9H10a.5.5 0 0 1 0 1H8.5v1.5a.5.5 0 0 1-1 0V10H6a.5.5 0 0 1 0-1h1.5V7.5A.5.5 0 0 1 8 7"/>
            <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
        </svg>
        </button>
    </div>

    <div class="container-fluid mt-3">
        <div class="overflow-auto emp_list">
            <?php
            $positionFilter = isset($_GET['position']) && !empty($_GET['position']) ? 'AND pos_id = ' . intval($_GET['position']) : '';
            $searchFilter = isset($_GET['search']) && !empty($_GET['search']) ? "AND (emp_fname LIKE '%" . mysqli_real_escape_string($conn, $_GET['search']) . "%' OR emp_lname LIKE '%" . mysqli_real_escape_string($conn, $_GET['search']) . "%')" : '';

            $sql = "SELECT emp_id, emp_fname, emp_lname 
                    FROM tbl_employee 
                    WHERE 1=1 $positionFilter $searchFilter
                    ORDER BY emp_lname ASC";
            $employeesResult = mysqli_query($conn, $sql);

            echo '<div id="employeeList" class="mt-3"><ul class="list-group">';
            if (mysqli_num_rows($employeesResult) > 0) {
                while ($row = mysqli_fetch_assoc($employeesResult)) {
                    $emp_id = htmlspecialchars($row['emp_id']);
                    $emp_name = htmlspecialchars($row['emp_lname'] . ', ' . $row['emp_fname']);
                    echo "<li class=\"list-group-item\"><a class=\"text-capitalize text-dark text-decoration-none\" href=\"emprecord.php?emp_id=$emp_id\">$emp_name</a></li>";
                }
            } else {
                echo '<li class="list-group-item">No employees found.</li>';
            }
            echo '</ul></div>';
            ?>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
        function openNewEmployeeWindow() {
            window.open('add_employee.php', 'Add New Employee', 'width=500,height=500');
        }
        function openNewPositionWindow() {
            window.open('add_position.php', 'Add New Position', 'width=550, height=450');
        }
        function openNewPeriodWindow() {
            window.open('add_period.php', 'Add New Period', 'width=500,height=510');
        }
    </script>
</div>
