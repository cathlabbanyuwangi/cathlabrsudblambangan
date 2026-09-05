<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CathLab') }} - Secure Login</title>

        <!-- Google Fonts: Inter untuk kesan Enterprise/Profesional -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* Memaksa penggunaan font Inter */
            body {
                font-family: 'Inter', sans-serif;
            }
        </style>
    </head>
    <!-- Background menggunakan gradasi sangat halus (slate-50 ke gray-100) -->
    <body class="text-gray-900 antialiased bg-gradient-to-br from-slate-50 to-gray-100 selection:bg-indigo-500 selection:text-white">
        
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-10 sm:pt-0">
            
            <!-- Logo Section -->
            <div class="mb-4 text-center">
                <a href="/" class="flex flex-col items-center gap-3 group transition-transform duration-300 hover:scale-105">
                    <!-- Kotak pembungkus logo agar terlihat seperti icon aplikasi -->
                    <div class="p-4 bg-white rounded-2xl shadow-sm border border-gray-100 group-hover:shadow-md transition-shadow duration-300">
                        <x-application-logo class="w-12 h-12 fill-current text-indigo-600" />
                    </div>
                    <!-- Nama Aplikasi -->
                    <span class="text-2xl font-extrabold tracking-tight text-gray-900">
                        {{ config('app.name', 'CathLab') }}
                    </span>
                </a>
            </div>

            <!-- Slot Konten (Login Form) -->
            <!-- Background putih dan shadow dihapus dari sini karena sudah ada di login.blade.php -->
            <div class="w-full sm:max-w-md relative z-10">
                {{ $slot }}
            </div>

            <!-- Footer / Copyright -->
            <div class="mt-10 text-center text-sm text-gray-400 font-medium">
                &copy; {{ date('Y') }} {{ config('app.name', 'CathLab') }}. All rights reserved.
            </div>

        </div>
    </body>
</html>