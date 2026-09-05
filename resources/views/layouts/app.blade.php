<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <!-- Vite CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body class="font-sans antialiased bg-slate-50/60 text-slate-700">
    <div class="min-h-screen flex">
        
        <!-- Sidebar Fixed -->
        @include('layouts.sidebar')

        <!-- Bagian Kanan (Konten Utama) -->
        <div class="flex-1 flex flex-col min-w-0">
            
            <!-- Navbar Atas -->
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white/80 backdrop-blur-md border-b border-slate-100 sticky top-0 z-20">
                    <div class="max-w-7xl mx-auto py-6 px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-1 p-8">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-slate-100 py-4 px-8 text-center text-xs text-slate-400 mt-auto">
                &copy; {{ date('Y') }} Cathlab Project - Banyuwangi. All rights reserved.
            </footer>
        </div>
    </div>

    <!-- SweetAlert2 JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Global SweetAlert Flash Message Handler -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false,
                    customClass: {
                        popup: 'rounded-[28px]'
                    }
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#e11d48',
                    customClass: {
                        popup: 'rounded-[28px]'
                    }
                });
            @endif

            @if(session('warning'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: "{{ session('warning') }}",
                    confirmButtonColor: '#f59e0b',
                    customClass: {
                        popup: 'rounded-[28px]'
                    }
                });
            @endif
        });
    </script>
</body>
</html>