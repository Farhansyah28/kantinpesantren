<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>General Error</title>
    <style>
        div.container:first-letter { text-transform: capitalize; }
        body {
            color: #333;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        div.container {
            max-width: 1024px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #d32f2f;
            font-size: 24px;
            font-weight: normal;
            margin: 0 0 10px 0;
            background: #d32f2f;
            color: #fff;
            padding: 20px;
            border-radius: 5px 5px 0 0;
        }
        #container {
            margin: 10px;
            border: 1px solid #d0d0d0;
            box-shadow: 0 0 8px #d0d0d0;
            border-radius: 5px;
        }
        p {
            margin: 8px 0;
        }
        .error {
            background: #f8f8f8;
            border: 1px solid #ddd;
            border-radius: 0 0 5px 5px;
            padding: 20px;
        }
        .error p {
            margin: 0;
            line-height: 1.5;
        }
        .error strong {
            color: #d32f2f;
        }
        .error .file {
            color: #666;
            font-family: monospace;
            font-size: 12px;
        }
        .error .line {
            color: #d32f2f;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div id="container">
            <h1><?php echo isset($heading) ? $heading : 'General Error'; ?></h1>
            <div class="error">
                <?php echo isset($message) ? $message : 'Terjadi kesalahan umum.'; ?>
            </div>
        </div>
    </div>
</body>
</html> 