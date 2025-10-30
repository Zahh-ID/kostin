<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'KostIn') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">{{ config('app.name', 'KostIn') }}</a>
        <div class="d-flex">
            @auth
                <form method="POST" action="{{ route('logout') }}" class="d-flex">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">Logout</button>
                </form>
            @endauth
            @guest
                <a class="btn btn-outline-primary" href="{{ route('login') }}">Login</a>
            @endguest
        </div>
    </div>
</nav>

<main>
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
