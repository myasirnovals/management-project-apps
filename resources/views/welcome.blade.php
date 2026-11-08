<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="0;url={{ route('login') }}">
    <title>{{ config('app.name', 'Capstone Project Manager') }}</title>
</head>
<body>
    <p><a href="{{ route('login') }}">Buka halaman masuk</a></p>
</body>
</html>
