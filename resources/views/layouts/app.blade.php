<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        @stack('styles')
        @livewireStyles

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            #sidebar.collapsed {
                width: 80px;
            }

            #sidebar.collapsed .nav-link span {
                display: none;
            }

            #sidebar.collapsed .nav-link .bi {
                font-size: 1.2rem;
            }

            #sidebar.collapsed .fs-4,
            #sidebar.collapsed .dropdown strong {
                display: none;
            }

            .sidebar-transition {
                transition: all 0.3s;
            }
        </style>
    </head>
    <body class="bg-light">
        <div class="d-flex flex-column min-vh-100">
            <div class="container-fluid flex-grow-1">
                <div class="row flex-nowrap">
                    <aside id="sidebar" class="col-12 col-md-4 col-lg-3 col-xxl-2 bg-white border-end p-0 @if(isset($_COOKIE['sidebar_collapsed']) && $_COOKIE['sidebar_collapsed'] == 'true') collapsed @endif">
                        <!-- layout.navigation -->
                        <livewire:layout.navigation />
                    </aside>

                    <main class="col py-4 px-3 px-lg-4">
                        @isset($header)
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                                {{ $header }}
                            </div>
                        @endisset

                        {{ $slot ?? '' }}
                        @yield('content')
                    </main>
                </div>
            </div>

            <footer class="bg-white border-top py-3">
                <div class="container-fluid text-center text-muted small">
                    &copy; {{ now()->year }} {{ config('app.name', 'KostIn') }}. Seluruh hak cipta.
                </div>
            </footer>
        </div>

        @livewireScripts
        @stack('scripts')
    </body>
</html>
