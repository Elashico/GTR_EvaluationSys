<?php
require('./template/header.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Remove position functionality
    if (isset($_POST['remove_position'])) {
        $position = mysqli_real_escape_string($conn, $_POST['position']);
        
        $sql_delete_position = "DELETE FROM tbl_positions WHERE position = '$position'";
        if (mysqli_query($conn, $sql_delete_position)) {
            echo "<script>alert('Position and related records removed successfully [$position]');window.opener.location.reload();</script>";
        } else {
            echo "Error deleting position: " . mysqli_error($conn);
        }

    }

    // Add position functionality
    if (isset($_POST['add_position'])) {
        $position = mysqli_real_escape_string($conn, $_POST['position']);

        $sql = "INSERT INTO tbl_positions (position) VALUES ('$position')";
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('New position added successfully [$position]');window.opener.location.reload();</script>";
        } else {
            echo "Error adding position: " . mysqli_error($conn);
        }
    }
}
?>

<div class="container">
    <h2 class="mt-3">Add Position</h2>
    <hr>
    <form action="" method="POST">
        <div class="form-group">
            <label for="position">Position Name</label>
            <input type="text" name="position" class="form-control" id="position" required>
        </div>
        <div class="mt-2 text-center">
            <button type="submit" name="add_position" class="btn btn-outline-primary mt-3">Add Position</button>
        </div>
    </form>

    <hr>

    <h2 class="mt-3">Remove Position</h2>
    <p class="fs-6 fw-semibold text-danger">**Removing a position will also remove all employees and their records under that position**</p>
    <form action="" method="POST">
        <div class="form-group">
            <label for="position">Position Name</label>
            <input type="text" name="position" class="form-control" id="position" required>
        </div>
        <div class="mt-2 text-center">
            <button type="submit" name="remove_position" class="btn btn-outline-danger my-3">Remove</button>
        </div>
    </form>
</div>

<?php
require('./template/footer.php');
?>
