<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 | Page Not Found</title>
    <style>
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
            background-color: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            color: #374151;
        }
        .container {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        h1 {
            font-size: 4rem;
            margin: 0;
            color: #ef4444;
        }
        p {
            font-size: 1.25rem;
            margin-top: 1rem;
        }
        .debug-info {
            margin-top: 2rem;
            text-align: left;
            font-size: 0.875rem;
            background: #f9fafb;
            padding: 1rem;
            border-radius: 0.25rem;
            border: 1px solid #e5e7eb;
        }
        .btn {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 0.5rem 1rem;
            background-color: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 0.25rem;
            transition: background-color 0.2s;
        }
        .btn:hover {
            background-color: #2563eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>404</h1>
        <p>Sorry, the page or API endpoint you are looking for does not exist.</p>
        
        <div class="debug-info">
            <strong>Message:</strong> {{ $exception->getMessage() ?: 'No additional error details available.' }} <br>
            <strong>Requested URL:</strong> {{ request()->fullUrl() }} <br>
            <strong>Time (Asia/Phnom_Penh):</strong> {{ now()->toDateTimeString() }}
        </div>

        <a href="/" class="btn">View API Documentation</a>
    </div>
</body>
</html>
