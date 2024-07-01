<?php require('./template/header.php'); ?>
<style>
    .body_{
        max-height: 100vh; /* Adjust this value as needed */
        overflow-y: auto;
        scrollbar-width: normal; /* Firefox */
        scrollbar-color: #595959 #f1f1f1; /* Firefox */
        overflow-x: hidden;
    }
    .body_::-webkit-scrollbar {
        width: 12px; /* For webkit browsers */
    }
</style>
            <?php
            if (isset($_GET['emp_id']) && !empty($_GET['emp_id'])) {
                $empId = intval($_GET['emp_id']);

                $sql = "SELECT e.*, p.position FROM tbl_employee e 
                        JOIN tbl_positions p ON e.pos_id = p.pos_id 
                        WHERE e.emp_id = $empId";
                $employeeResult = mysqli_query($conn, $sql);

                if ($row = mysqli_fetch_assoc($employeeResult)) {
                    echo '<div class="mt-3 container-fluid d-flex justify-content-between">';
                        echo '<div class="text-start">';
                            echo '<h1 class="mt-2 lead fw-medium fs-3">Employee Profile</h1>';
                        echo '</div>';
                        echo '<div class="text-end">';
                            echo '<button type="button" class="mt-2 btn btn-outline-danger btn-xm" onclick="confirmDelete()" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Remove Employee">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill-x" viewBox="0 0 16 16">
                                <path d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0m-9 8c0 1 1 1 1 1h5.256A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1 1.544-3.393Q8.844 9.002 8 9c-5 0-6 3-6 4"/>
                                <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m-.646-4.854.646.647.646-.647a.5.5 0 0 1 .708.708l-.647.646.647.646a.5.5 0 0 1-.708.708l-.646-.647-.646.647a.5.5 0 0 1-.708-.708l.647-.646-.647-.646a.5.5 0 0 1 .708-.708"/>
                            </svg>
                            </button>';
                        echo '</div>';
                    echo '</div>';
                    echo '<hr>';
                    echo '<p class="lead text-capitalize fs-5"> Name: <strong>' . htmlspecialchars($row['emp_lname'] . ', ' . $row['emp_fname'] . ' ' . $row['emp_minitial']) . '.</strong></p>';
                    echo '<p class="lead fs-5" > Position: <strong>' . htmlspecialchars($row['position']) . '</strong></p>';
                    $date = new DateTime($row['emp_date_hired']);
                    $formatted_date = $date->format('m-d-Y');
                    echo '<p class="lead fs-5"> Date Hired: <strong>' . htmlspecialchars($formatted_date) . '</strong></p>';
                } else {
                    echo 'Employee not found.';
                }

                $periodSql = "SELECT period_id, period FROM tbl_eval_period ORDER BY STR_TO_DATE(SUBSTRING_INDEX(period, ' ', -1), '%Y')";
                $periodsResult = mysqli_query($conn, $periodSql);

                echo '<form action="" method="GET">';
                echo '<input type="hidden" name="emp_id" value="' . $empId . '">';
                    echo '<div class="input-group mb-3">';
                        echo '<label for="period_id" class="input-group-text lead" >Evaluation Period</label>';
                        echo '<select class="form-select" name="period_id" id="period_id">';
                            echo '<option value="">select evaluation period</option>';

                            if (mysqli_num_rows($periodsResult) > 0) {
                                while ($periodRow = mysqli_fetch_assoc($periodsResult)) {
                                    $selected = isset($_GET['period_id']) && $_GET['period_id'] == $periodRow['period_id'] ? ' selected' : '';
                                    echo '<option value="' . htmlspecialchars($periodRow['period_id']) . '"' . $selected . '>' . htmlspecialchars($periodRow['period']) . '</option>';
                                }
                            } else {
                                echo '<option value="">No periods found</option>';
                            }

                        echo '</select>';
                    echo '</div>';
                echo '</form>';
                echo '<hr class="my-4">';

                // show evaluation based on selected period
                        }?>
                <div id="evaluation-details">
                <!-- Evaluation details will be loaded here dynamically -->
                </div>


<script>
function confirmDelete() {
    const empId = <?php echo isset($empId) ? $empId : 'null'; ?>;
    if (empId) {
        if (confirm("Are you sure you want to delete this employee [<?php echo htmlspecialchars($row['emp_lname'] . ', ' . $row['emp_fname'] . ' ' . $row['emp_minitial']);?>]? This action cannot be undone.")) {
            window.location.href = `./del_employee.php?emp_id=${empId}`;
        }
    }
}
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
function loadEvaluationDetails() {
    const empId = <?php echo isset($empId) ? $empId : 'null'; ?>;
    const periodId = document.getElementById('period_id').value;

    if (empId && periodId) {
        const xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById('evaluation-details').innerHTML = this.responseText;
            }
        };
        xhttp.open('GET', `evaluation_details.php?emp_id=${empId}&period_id=${periodId}`, true);
        xhttp.send();
    }
}
// Function to save evaluation and reload details
function saveEvaluation() {
        // Example: AJAX request to save evaluation
        // After saving, reload evaluation details
        loadEvaluationDetails();
}

function openEvaluationWindow() {
    const empId = <?php echo isset($empId) ? $empId : 'null'; ?>;
    const periodId = document.getElementById('period_id').value;
    if (empId && periodId) {
        window.open(`./add_evaluation.php?emp_id=${empId}&period_id=${periodId}`, 'Add Evaluation', 'width=900,height=760');
    }
}
function openPrintWindow() {
    const empId = <?php echo isset($empId) ? $empId : 'null'; ?>;
    const periodId = document.getElementById('period_id').value;
    if (empId && periodId) {
        let printWindow = window.open(`./printing/print_employee.php?emp_id=${empId}&period_id=${periodId}`, 'Print Employee', 'width=1000,height=650');
        printWindow.onload = function() {
            printWindow.print();
        };
    }
}

// Trigger AJAX load on dropdown change
document.getElementById('period_id').addEventListener('change', loadEvaluationDetails);
</script>

<?php
    require('./template/footer.php');
?>

