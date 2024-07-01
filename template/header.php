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

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- icon  -->
    <link rel="icon" href="./styles/people_icon.svg">
    <!-- stles  -->
    <link href="./styles/stylesmain.css" rel="stylesheet" type="text/css"  media="all"/>
    <!-- for printing in records only  -->
    <link href="./printing/print.css" rel="stylesheet" type="text/css" media="print"/>
    <!-- bootstrap  -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    
</head>
<body>