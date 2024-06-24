<?php
// save_evaluation.php

require './template/header.php';

// Ensure form fields are present and not empty
if (
    isset($_POST['emp_id']) && !empty($_POST['emp_id']) &&
    isset($_POST['period_id']) && !empty($_POST['period_id']) &&
    isset($_POST['supervisor_count']) && !empty($_POST['supervisor_count'])
) {
    // Sanitize inputs
    $empId = intval($_POST['emp_id']);
    $periodId = intval($_POST['period_id']);
    $supervisorCount = intval($_POST['supervisor_count']);
    
    // Assuming these are all numeric values, sanitize and validate accordingly
    $scores = [];
    $violationComment = isset($_POST['violation_comment']) ? $_POST['violation_comment'] : '';
    $recommendationComment = isset($_POST['comment_recc']) ? $_POST['comment_recc'] : '';
    
    // Collect scores from s1 to s13
    for ($i = 1; $i <= 13; $i++) {
        if (isset($_POST['s' . $i])) {
            $scores[$i] = intval($_POST['s' . $i]);
        } else {
            // Handle missing score if needed
            $scores[$i] = 0; // Assuming default value
        }
    }

    // Prepare and execute the SQL INSERT statement
    $sql = "INSERT INTO tbl_evaluation (emp_id, period_id, supi_count, s1, s2, s3, s4, s5, s6, s7, s8, s9, s10, s11, s12, s13, violation_comment, comment_recc)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    // Bind parameters
    mysqli_stmt_bind_param($stmt, "iiiiiiiiiiiiiiiiss",
        $empId,
        $periodId,
        $supervisorCount,
        $scores[1],
        $scores[2],
        $scores[3],
        $scores[4],
        $scores[5],
        $scores[6],
        $scores[7],
        $scores[8],
        $scores[9],
        $scores[10],
        $scores[11],
        $scores[12],
        $scores[13],
        $violationComment,
        $recommendationComment
    );
    
    // Execute the statement
    mysqli_stmt_execute($stmt);

    // Check for errors
    if (mysqli_stmt_error($stmt)) {
        die('Error executing MySQL statement: ' . mysqli_stmt_error($stmt));
    } else {
        echo "<script>alert('Evaluation SAVED');window.close();</script>";
    }
    
    // Close statement and connection
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
} else {
    die('Invalid parameters or missing form data.');
}

?>
