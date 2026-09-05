<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CathLab') }} - Secure Login</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                font-family: 'Inter', sans-serif;
            }
        </style>
    </head>
    <body class="text-slate-800 antialiased bg-gradient-to-br from-sky-50/60 via-slate-50 to-indigo-50/40 selection:bg-sky-200 selection:text-slate-800">
        
        <div class="min-h-screen flex flex-col sm:justify-center items-center py-10 px-4 sm:px-0">
            
            <div class="mb-6 text-center">
                <a href="/" class="flex flex-col items-center gap-3 group transition-transform duration-300 hover:scale-102">
                    <div class="p-3.5 bg-white/80 backdrop-blur-md rounded-2xl shadow-sm border border-sky-100 group-hover:shadow-md transition-all duration-300">
                        <x-application-logo class="w-10 h-10 fill-current text-sky-600" />
                    </div>
                    <span class="text-xl font-bold tracking-tight text-slate-800">
                        {{ config('app.name', 'CathLab') }}
                    </span>
                </a>
            </div>

            <div class="w-full sm:max-w-md relative z-10">
                {{ $slot }}
            </div>

            <div class="mt-8 text-center text-xs text-slate-400 font-medium">
                &copy; {{ date('Y') }} {{ config('app.name', 'CathLab') }}. All rights reserved.
            </div>

        </div>
    </body>
</html>