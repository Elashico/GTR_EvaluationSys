<?php
require('./template/header.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $position = mysqli_real_escape_string($conn, $_POST['position']);

    $sql = "INSERT INTO tbl_positions (position) VALUES ('$position')";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('New position added successfully'); window.close();</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<div class="container">
    <h2 class="mt-3">Add New Position</h2>
    <form action="" method="POST">
        <div class="form-group">
            <label for="position">Position Name</label>
            <input type="text" name="position" class="form-control" id="position" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Position</button>
    </form>
</div>

<?php
require('./template/footer.php');
?>
