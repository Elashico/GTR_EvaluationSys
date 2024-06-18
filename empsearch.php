<?php 
    require('./template/header.php'); //----------------
?>
<div>
    <h1>Find Emplyee</h1>
</div>

<form action="./emprecord.php" method="$_GET">
    <select name="position" required>
        <option value="">Select Position</option>
        <option value="Manager">Manager</option>
        <option value="Developer">Developer</option>
        <option value="Designer">Designer</option>
    </select>
    <button type="submit">ok</button>
</form>

<?php 
   require('./template/footer.php'); //---------------
?>