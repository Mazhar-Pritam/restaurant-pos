<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Restaurant POS')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
@auth
    <nav class="navbar navbar-dark bg-dark px-3">
        <a class="navbar-brand" href="{{ route('dashboard') }}">Restaurant POS</a>
        <div class="d-flex align-items-center gap-3">
            <span class="text-white-50 small">
                {{ auth()->user()->name }} ({{ auth()->user()->role?->label ?? 'No role' }})
            </span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-outline-light btn-sm">Logout</button>
            </form>
        </div>
    </nav>
@endauth

<main class="container py-4">
    @yield('content')
</main>
</body>
</html>
