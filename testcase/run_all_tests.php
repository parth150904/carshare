<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarShare - Test Suite</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; padding: 40px; color: #333; }
        .container { max-width: 800px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h1 { color: #2e7d32; border-bottom: 2px solid #e8f5e9; padding-bottom: 10px; margin-top: 0; }
        .test-block { background: #fafafa; border: 1px solid #eee; padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        h3 { margin-top: 0; color: #1565c0; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #777; }
    </style>
</head>
<body>

<div class="container">
    <h1>CarShare Test Suite Runner</h1>
    <p>Running all automated system checks...</p>
    
    <div class="test-block">
        <?php include 'test_db_connection.php'; ?>
    </div>

    <div class="test-block">
        <?php include 'test_fare_estimator.php'; ?>
    </div>

    <div class="footer">
        Test execution completed at <?php echo date('Y-m-d H:i:s'); ?>
    </div>
</div>

</body>
</html>

