<?php

    $conn = mysqli_connect("localhost","gtr-test","qwerty","db_gtr_evalsys");
    if (!$conn) {
        echo "DB connection error" . mysqli_connect_error();
    }


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GT-Evaluation</title>

    <!-- stles  -->
    <link href="./styles/stylesmain.css" rel="stylesheet" type="text/css" />
    <!-- bootstrap  -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
</head>
<body>