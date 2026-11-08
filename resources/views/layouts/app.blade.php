<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIMADOK') - SMK N 5 Surakarta</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 300: '#93c5fd',
                            400: '#60a5fa', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8',
                            800: '#1e40af', 900: '#1e3a8a',
                        }
                    },
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui'] },
                    animation: {
                        'fade-in': 'fadeIn .4s ease-out',
                        'slide-up': 'slideUp .4s ease-out',
                    },
                    keyframes: {
                        fadeIn: { '0%': { opacity: 0 }, '100%': { opacity: 1 } },
                        slideUp: { '0%': { opacity: 0, transform: 'translateY(10px)' }, '100%': { opacity: 1, transform: 'translateY(0)' } },
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-thumb {
            background: #93c5fd;
            border-radius: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #eff6ff;
        }

        .table-row-hover:hover {
            background-color: #eff6ff;
            transition: background-color .15s ease;
        }
    </style>
    @stack('styles')
</head>

{{-- FIX: overflow-x-hidden sebagai pengaman agar halaman tidak bisa bergeser ke samping --}}

<body class="bg-slate-50 text-slate-800 min-h-screen overflow-x-hidden">

    <div class="flex min-h-screen">
        {{-- SIDEBAR --}}
        <aside id="sidebar"
            class="fixed z-30 inset-y-0 left-0 w-64 bg-gradient-to-b from-primary-800 to-primary-900 text-white transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
            <div class="flex items-center gap-3 px-6 py-5 border-b border-primary-700/50">
                <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center overflow-hidden shrink-0">
                    <img src="{{ asset('images/logo-sekolah.png') }}" alt="Logo SMK Negeri 5 Surakarta"
                        class="w-8 h-8 object-contain">
                </div>
                <div>
                    <p class="font-bold leading-tight">SIMADOK</p>
                    <p class="text-xs text-primary-200">SMK N 5 Surakarta</p>
                </div>
            </div>
            <nav class="px-3 py-4 space-y-1">
                @yield('sidebar-menu')
            </nav>
            <div class="absolute bottom-0 w-full p-4 border-t border-primary-700/50">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        class="w-full flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-700/60 hover:bg-primary-700 transition text-sm font-medium">
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        <div id="overlay" class="fixed inset-0 bg-black/40 z-20 hidden lg:hidden"></div>

        {{-- MAIN --}}
        {{-- FIX: tambah min-w-0 agar kolom utama boleh menyempit sesuai lebar layar --}}
        <div class="flex-1 min-w-0 lg:ml-64 flex flex-col min-h-screen">
            <header
                class="sticky top-0 z-10 bg-white/80 backdrop-blur border-b border-slate-200 px-4 sm:px-6 py-3 flex items-center justify-between gap-3">
                {{-- FIX: min-w-0 agar judul bisa dipotong (truncate) di layar kecil --}}
                <div class="flex items-center gap-3 min-w-0">
                    <button id="menuBtn" class="lg:hidden p-2 rounded-lg hover:bg-slate-100 shrink-0">
                        <svg class="w-6 h-6 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="text-lg sm:text-xl font-bold text-primary-800 truncate">@yield('page-title', 'Dashboard')
                    </h1>
                </div>
                {{-- FIX: shrink-0 supaya avatar tidak ikut terhimpit --}}
                <div class="flex items-center gap-3 shrink-0">
                    <span
                        class="hidden sm:block text-sm text-slate-500 whitespace-nowrap">{{ now()->translatedFormat('l, d F Y') }}</span>
                    <div
                        class="w-9 h-9 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold">
                        {{ strtoupper(substr($__env->yieldContent('user-initial', 'U'), 0, 1)) }}
                    </div>
                </div>
            </header>

            {{-- FIX: min-w-0 agar konten (tabel, kartu) tidak melebarkan halaman --}}
            <main class="flex-1 min-w-0 p-4 sm:p-6 animate-fade-in">    
                @if(session('status'))
                    <div
                        class="mb-4 rounded-lg border border-primary-200 bg-primary-50 text-primary-800 px-4 py-3 text-sm animate-slide-up">
                        {{ session('status') }}
                    </div>
                @endif
                @yield('content')
            </main>

            <footer class="text-center text-xs text-slate-400 py-4 px-4">
                &copy; {{ date('Y') }} SIMADOK — Sistem Manajemen Dokumen Akademis SMK N 5 Surakarta
            </footer>
        </div>
    </div>

    <script>
        const menuBtn = document.getElementById('menuBtn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        function toggleSidebar() {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
        menuBtn?.addEventListener('click', toggleSidebar);
        overlay?.addEventListener('click', toggleSidebar);
    </script>
    @stack('scripts')
</body>

</html>