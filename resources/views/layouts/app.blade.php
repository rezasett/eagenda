<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'E-Agenda') }}</title>
    <link rel="stylesheet" href="{{ asset('css/eagenda.css') }}">
    @livewireStyles
</head>
<body>
    <div class="app-shell">
        @auth
            <aside class="sidebar">
                <div class="brand">
                    <div class="brand-logo">EA</div>
                    <div>
                        <strong>E-Agenda</strong>
                        <span>Administrasi Surat</span>
                    </div>
                </div>

                <nav class="nav-menu">
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
                    <a href="{{ route('letters.index') }}" class="{{ request()->routeIs('letters.index') ? 'active' : '' }}">Data Surat</a>
                </nav>

                <div class="user-card">
                    <strong>{{ auth()->user()->name }}</strong>
                    <span>{{ auth()->user()->role === 'staff' ? 'Staf Administrasi' : 'Pimpinan' }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="button button-light button-block">Logout</button>
                    </form>
                </div>
            </aside>
        @endauth

        <main class="main-content {{ auth()->check() ? '' : 'guest-main' }}">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>
