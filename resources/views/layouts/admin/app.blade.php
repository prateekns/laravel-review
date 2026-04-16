<!DOCTYPE html>
<html class="h-full bg-gray-50" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - Super Admin @yield('title')</title>
    <!-- Alpine.js CDN -->
    @vite(['resources/css/admin.css','resources/js/admin/admin.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.9/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    @livewireStyles
</head>

<body class="h-full">
    <div>
        @include('layouts.admin.partials.sidebar')
        <div class="lg:pl-72">
            <livewire:admin.header />
            <main class="py-7 bg-gray-100">
                <div class="px-4 sm:px-6 lg:px-8">
                    @yield('content')
                </div>
            </main>
            @yield('loading')
        </div>
    </div>
    @livewireScripts
</body>
<script>
    Livewire.hook('request', ({
        fail
    }) => {
        fail(({
            status,
            preventDefault
        }) => {
            if (status === 419) {
                preventDefault()
                window.location.reload();
            }
        })
    });
</script>

</html>
