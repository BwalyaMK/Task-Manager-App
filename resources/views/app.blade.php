<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager</title>
    @vite('resources/js/app.js', 'resources/css/app.css')
</head>
<body class="antialiased">
    @inertia
</body>
</html>
