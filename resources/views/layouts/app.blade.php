<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Celovel Framework')</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .content {
            padding: 30px;
        }
        .feature-list {
            list-style: none;
            padding: 0;
        }
        .feature-list li {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .feature-list li:before {
            content: "✓ ";
            color: #667eea;
            font-weight: bold;
        }
        .nav {
            background: #f8f9fa;
            padding: 15px 30px;
            border-bottom: 1px solid #dee2e6;
        }
        .nav a {
            color: #667eea;
            text-decoration: none;
            margin-right: 20px;
            font-weight: 500;
        }
        .nav a:hover {
            color: #764ba2;
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>@yield('title', 'Celovel Framework')</h1>
            <p>@yield('subtitle', 'Laravel benzeri modern PHP framework')</p>
        </div>
        
        @if($showNav ?? false)
        <div class="nav">
            <a href="{{ url('/') }}">Ana Sayfa</a>
            <a href="{{ url('/about') }}">Hakkında</a>
            <a href="{{ url('/test') }}">Test</a>
            <a href="{{ url('/api/status') }}">API Status</a>
        </div>
        @endif
        
        <div class="content">
            @yield('content')
        </div>
    </div>
    
    @yield('scripts')
</body>
</html>
