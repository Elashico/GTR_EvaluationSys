<?php
    require('./template/header.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Assuming you have established database connection already
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        
        $sql = "SELECT * FROM tbl_users WHERE username = '$username' AND password = '$password'";
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) == 1) {
            echo "<script>window.location.href = './mainpage.php';</script>";
            exit();
        } else {
            echo '<script>alert("Invalid username or password"); window.history.back();</script>';
        }
    }
?>

<div class="body_">
    <div class="row">
        <div class="col-md-3 side_navigation sticky-sm-top">
            <?php require('./empsearch.php'); ?>
        </div>
        <div class="col-md-8" id="employeeRecords">
            <?php require('./mainrecord.php'); ?>
        </div>
    </div>
</div>

<!-- Add this script block at the end of your mainpage.php, just before </body> -->
<script>
$(document).ready(function() {
    // Handle click events on employee links
    $('.employee-link').click(function(e) {
        e.preventDefault(); // Prevent default link behavior
        
        var empId = $(this).data('emp-id'); // Get emp_id from data attribute
        
        // Send AJAX request to mainrecord.php
        $.ajax({
            type: 'GET',
            url: 'mainrecord.php',
            data: { emp_id: empId },
            success: function(response) {
                $('#employeeRecords').html(response); // Update employeeRecords div with new content
            },
            error: function() {
                alert('Error loading employee record.');
            }
        });
    });
});
</script>

<?php
    require('./template/footer.php');
?>


