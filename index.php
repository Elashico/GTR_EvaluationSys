<?php 
   require('./template/header.php'); //---------------
?>

<div class="text-center">
    <h1 class="display-1">Employee Evaluation</h1>
</div>

<div class="login-container text-center"></div>
    <form action="./emprecord.php" method="$_GET">
        <fieldset class="text-center d-flex flex-column mb-3" id="fd-login">
            <legend class="h5">Enter Required: </legend>
            <input class="mb-3" type="text" placeholder="Username" required />
            <input class="mb-3" type="password" placeholder="Password" required />
            <button class="mb-3" type="submit">Login</button>
        </fieldset>
    </form>
</div>

<?php 
   require('./template/footer.php'); //---------------
?>
