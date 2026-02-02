<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <title>Page Not Found - Softifyx</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Jost', sans-serif;
            background: #02ac05;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 60px 40px;
            max-width: 500px;
            text-align: center;
        }
        .error-code {
            font-size: 120px;
            font-weight: 900;
            color: #667eea;
            line-height: 1;
            margin-bottom: 20px;
        }
        h1 {
            font-size: 32px;
            color: #333;
            margin-bottom: 15px;
        }
        p {
            font-size: 16px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        a, button {
            display: inline-block;
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #02ac05;
            transform: translateY(-2px);
        }
        .btn-secondary {
            background: #f0f0f0;
            color: #333;
            border: 2px solid #ddd;
        }
        .btn-secondary:hover {
            background: #e8e8e8;
        }
        .subdomain-info {
            background: #f9f9f9;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 30px 0;
            text-align: left;
            border-radius: 5px;
        }
        .subdomain-info p {
            margin: 5px 0;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            color: #555;
        }
        .subdomain-info strong {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-code">404</div>
        <h1>Oops! Page Not Found</h1>
        <p>The page you're looking for doesn't exist yet. Would you like to create one?</p>

        @isset($subdomain)
        <div class="subdomain-info">
            <p><strong>Subdomain:</strong> {{ $subdomain }}</p>
            <p><strong>Status:</strong> Not registered in our system</p>
        </div>
        @endisset

        <div class="actions">
            <a href="http://softifyx.localhost:8000" class="btn-primary">← Back to Home</a>
            <a href="http://softifyx.localhost:8000/register-company" class="btn-primary">Create Account</a>
        </div>

        <p style="margin-top: 40px; font-size: 14px; color: #999;">
            If you believe this is a mistake, please contact support. @<a href="tel:+2547110848885">+254 110848885</a>

        </p>
    </div>
</body>
</html>
