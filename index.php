<?php require('./template/header.php'); ?>

<style>
    body {
        background-image: url('styles/GTRBLDG.jpg');
        background-size: cover;
        background-position: center;
        height: 100vh;
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    .login-form {
        background-color: rgba(255, 255, 255, 0.9);
        padding: 30px 30px 20px 30px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        text-align: center;
        max-width: 550px;
        width: 100%;
    }
    
    .logo {
        max-width: 200px;
        margin-bottom: 20px;
    }
</style>

<div class="login-form">
    <img src="styles/GTRLOGO.png" alt="Company Logo" class="logo">
    <h3 class="display-5 mb-4">Employee Evaluation</h3>
    
    <form action="emprecord.php" method="post">
        <fieldset class="text-center d-flex flex-column mb-3">
            <hr>
            <input name="username" class="mb-3 mt-3 form-control" type="text" placeholder="Username" style="width: 80%; margin-left:10%" required>
            <input name="password" class="mb-3 form-control" type="password" placeholder="Password" style="width: 80%; margin-left:10%" required>
            <button class="mb-3 mt-4 btn btn-outline-dark" type="submit" style="width: 50%; margin-left:25%">Login</button>
        </fieldset>
    </form>
</div>

<?php require('./template/footer.php'); ?>