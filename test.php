<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Popup</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <button class="btn btn-primary" onclick="openEvaluationWindow()">Add Evaluation</button>

    <script>
        function openEvaluationWindow() {
            // Hardcoded values for testing
            var empId = 1;
            var periodId = 2;
            var url = 'add_evaluation.php?emp_id=' + empId + '&period_id=' + periodId;
            window.open(url, 'Add Evaluation', 'width=800,height=600');
        }
    </script>
</body>
</html>
