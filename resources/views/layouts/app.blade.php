<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Proposition Logic Calculator')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .card-glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .logic-operator {
            background: linear-gradient(45deg, #4361ee, #3a0ca3);
            color: white;
            padding: 4px 12px;
            border-radius: 6px;
            margin: 0 2px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .logic-operator:hover {
            transform: translateY(-2px);
        }
        
        .expression-input {
            font-family: 'Courier New', monospace;
            font-size: 1.2rem;
            padding: 15px;
            border: 2px solid #dee2e6;
            border-radius: 10px;
            transition: all 0.3s;
        }
        
        .expression-input:focus {
            border-color: #4361ee;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.25);
        }
        
        .truth-table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .truth-table th {
            background: #4361ee;
            color: white;
            padding: 12px;
            text-align: center;
        }
        
        .truth-table td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }
        
        .result-true {
            background: #d4edda !important;
            color: #155724;
            font-weight: bold;
        }
        
        .result-false {
            background: #f8d7da !important;
            color: #721c24;
            font-weight: bold;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="container py-4">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/d3@7"></script>
    @stack('scripts')
</body>
</html>