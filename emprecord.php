<?php 
    require('./template/header.php'); //----------------
?>
<!--  side nav  -->
<div>
    <div> 
        <h1 class="mt-3">Find Employee</h1>
    </div>

    <div> 
        <form action="./emprecord.php" method="$_GET">
            <select name="position" required>
                <option value="">Select Position</option>
                <option value="Manager">Manager</option>
                <option value="Developer">Developer</option>
                <option value="Designer">Designer</option>
            </select>
            <button type="submit">ok</button>
        </form>
    </div>
</div>

<!-- Main  -->
<div>


</div>
<h1>records</h1>

<?php 
   require('./template/footer.php'); //---------------
?>