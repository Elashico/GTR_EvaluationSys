<?php
require('./template/header.php'); // Adjust this according to your configuration file

if (isset($_GET['emp_id']) && !empty($_GET['emp_id'])) {
    $empId = intval($_GET['emp_id']);
    
    $deleteSql = "DELETE FROM tbl_employee WHERE emp_id = $empId";
    if (mysqli_query($conn, $deleteSql)) {
        header("Location: mainpage.php");
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete employee.']);
    }

    mysqli_close($conn);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Employee ID not provided.']);
}
?>
<div></div>
