<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#2454C7">
    <title>@yield('title') - SIMADOK</title>

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ink: '#102F5C',
                        brand: {
                            50: '#EEF4FF', 100: '#DCE8FF', 200: '#C3D5FF', 300: '#91B5FF',
                            400: '#5E8DF5', 500: '#2454C7', 600: '#1E45AA', 700: '#17368A', 800: '#122D6F',
                        },
                        teal: {
                            50: '#ECFEFF', 100: '#CFFAFE', 500: '#0EA5B7', 600: '#0B8695', 700: '#096C78',
                        },
                        amber: {
                            50: '#FFF8E8', 100: '#FDECC5', 500: '#EFA330', 600: '#D6871A',
                        }
                    },
                    fontFamily: {
                        display: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui'],
                        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                    },
                    boxShadow: {
                        soft: '0 20px 60px rgba(15, 46, 90, .08)',
                        card: '0 18px 50px rgba(15, 46, 90, .10)',
                        button: '0 10px 25px rgba(36, 84, 199, .25)',
                        glow: '0 0 0 1px rgba(94,141,245,.25), 0 24px 60px rgba(36,84,199,.18)',
                    }
                }
            }
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }

        h1,
        h2,
        h3,
        .font-display {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* ---------- Teks gradien (sama dengan beranda) ---------- */
        .grad-text {
            background: linear-gradient(100deg, #1E45AA 0%, #0EA5B7 45%, #2454C7 90%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        /* ---------- Grid latar (sama dengan beranda) ---------- */
        .grid-bg {
            background-image:
                linear-gradient(to right, rgba(36, 84, 199, .06) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(36, 84, 199, .06) 1px, transparent 1px);
            background-size: 44px 44px;
            -webkit-mask-image: radial-gradient(ellipse 70% 55% at 50% 0%, #000 30%, transparent 75%);
            mask-image: radial-gradient(ellipse 70% 55% at 50% 0%, #000 30%, transparent 75%);
        }

        @keyframes riseIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .rise-in {
            animation: riseIn .6s ease both;
        }

        .field-icon {
            pointer-events: none;
        }

        /* ---------- Overlay panel kiri: blur merata + tint terang ---------- */
        .pb {
            position: absolute;
            inset: 0;
            pointer-events: none;
        }

        .pb-blur {
            -webkit-backdrop-filter: blur(4px) saturate(1.05);
            backdrop-filter: blur(4px) saturate(1.05);
        }

        /* Terang di area teks (atas), memudar ke foto yang jelas (bawah) */
        .pb-tint {
            background: linear-gradient(to bottom,
                    rgba(247, 250, 255, .95) 0%,
                    rgba(247, 250, 255, .90) 36%,
                    rgba(247, 250, 255, .70) 52%,
                    rgba(247, 250, 255, .30) 66%,
                    rgba(247, 250, 255, .08) 82%,
                    rgba(247, 250, 255, .16) 100%);
        }

        @media (prefers-reduced-motion: reduce) {

            .rise-in,
            .animate-ping {
                animation: none !important;
            }
        }
    </style>
</head>

<body class="min-h-screen bg-[#F7FAFF] text-slate-800 antialiased lg:grid lg:grid-cols-2">

    {{-- LATAR (sama dengan beranda) --}}
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -left-32 -top-32 h-[30rem] w-[30rem] rounded-full bg-brand-300/35 blur-[90px]"></div>
        <div class="absolute -right-32 top-[30%] h-[28rem] w-[28rem] rounded-full bg-teal-500/25 blur-[90px]"></div>
        <div class="absolute bottom-[-8rem] left-[25%] h-[25rem] w-[25rem] rounded-full bg-amber-500/15 blur-[90px]">
        </div>
        <div class="grid-bg absolute inset-0"></div>
    </div>

    {{-- PANEL KIRI: FOTO SEKOLAH --}}
    <section class="relative hidden min-h-screen overflow-hidden px-14 py-12 lg:block">
        <img src="{{ asset('images/bg_sekolah.jpg') }}" alt="SMK Negeri 5 Surakarta"
            class="absolute inset-0 h-full w-full object-cover">

        {{-- Blur merata + tint terang + grid --}}
        <div class="pb pb-blur"></div>
        <div class="pb pb-tint"></div>
        <div class="pb grid-bg"></div>

        <div class="relative z-10 max-w-xl">
            {{-- logo + nama --}}
            <div class="rise-in flex items-center gap-3">
                <img src="{{ asset('images/logo-sekolah.png') }}" alt="Logo SMK Negeri 5 Surakarta"
                    class="h-12 w-12 object-contain">
                <div class="leading-tight">
                    <p class="font-display text-lg font-extrabold tracking-tight text-ink">SIMADOK</p>
                    <p class="mt-0.5 text-xs font-medium text-slate-600">SMK N 5 Surakarta</p>
                </div>
            </div>

            {{-- badge --}}
            <div
                class="rise-in mt-9 inline-flex items-center gap-2.5 rounded-full border border-brand-100 bg-white/85 py-1.5 pl-3 pr-4 text-xs font-bold uppercase tracking-[0.14em] text-brand-600 shadow-sm backdrop-blur">
                <span class="relative flex h-2 w-2">
                    <span
                        class="absolute inline-flex h-full w-full animate-ping rounded-full bg-teal-500 opacity-60"></span>
                    <span class="relative inline-flex h-2 w-2 rounded-full bg-teal-500"></span>
                </span>
                Sistem Manajemen Dokumen Akademik
            </div>

            {{-- headline --}}
            <h2
                class="rise-in mt-5 font-display text-4xl font-extrabold leading-[1.1] tracking-[-0.035em] text-ink xl:text-[2.75rem]">
                Satu sistem untuk seluruh
                <span class="grad-text">dokumen akademik</span>
                siswa.
            </h2>

            {{-- deskripsi --}}
            <p class="rise-in mt-4 max-w-md text-base font-medium leading-7 text-slate-700">
                Raport, ijazah, dan sertifikat tersimpan rapi, aman, dan bisa ditelusuri kapan saja.
            </p>

            {{-- fitur --}}
            <div class="rise-in mt-6 flex flex-wrap gap-2.5">
                <span
                    class="inline-flex items-center gap-2 rounded-xl border border-brand-100 bg-white/85 py-1.5 pl-1.5 pr-3.5 text-sm font-semibold text-ink shadow-sm backdrop-blur">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-brand-50 text-brand-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </span>
                    Raport
                </span>
                <span
                    class="inline-flex items-center gap-2 rounded-xl border border-brand-100 bg-white/85 py-1.5 pl-1.5 pr-3.5 text-sm font-semibold text-ink shadow-sm backdrop-blur">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-teal-50 text-teal-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                        </svg>
                    </span>
                    Ijazah
                </span>
                <span
                    class="inline-flex items-center gap-2 rounded-xl border border-brand-100 bg-white/85 py-1.5 pl-1.5 pr-3.5 text-sm font-semibold text-ink shadow-sm backdrop-blur">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                    </span>
                    Sertifikat
                </span>
            </div>
        </div>

        {{-- footer --}}
        <p
            class="absolute bottom-6 left-14 z-10 rounded-full border border-brand-100 bg-white/85 px-3 py-1 text-xs font-medium text-slate-600 backdrop-blur">
            &copy; {{ date('Y') }} SMK Negeri 5 Surakarta
        </p>
    </section>

    {{-- PANEL KANAN: FORM --}}
    <section class="flex min-h-screen items-center justify-center px-6 py-12 sm:px-10">
        <div class="rise-in w-full max-w-md">

            {{-- logo untuk mobile --}}
            <div class="mb-8 flex items-center gap-3 lg:hidden">
                <img src="{{ asset('images/logo-sekolah.png') }}" alt="Logo SMK Negeri 5 Surakarta"
                    class="h-11 w-11 object-contain">
                <div class="leading-tight">
                    <p class="font-display text-base font-extrabold tracking-tight text-ink">SIMADOK</p>
                    <p class="mt-0.5 text-[11px] font-medium text-slate-500">SMK N 5 Surakarta</p>
                </div>
            </div>

            {{-- KARTU FORM --}}
            <div class="rounded-3xl border border-brand-100 bg-white/80 p-6 shadow-card backdrop-blur sm:p-8">

                {{-- SWITCH ROLE --}}
                <div
                    class="mb-8 flex gap-1 rounded-full border border-brand-100 bg-brand-50/70 p-1 text-sm font-semibold">
                    <a href="{{ route('admin.login') }}"
                        class="flex flex-1 items-center justify-center gap-1.5 rounded-full py-2.5 text-center transition
                        {{ request()->routeIs('admin.login') ? 'bg-brand-600 text-white shadow-button' : 'text-slate-500 hover:bg-white hover:text-brand-700' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.5l2 2 4-4.5M12 3l7 3.2v5.1c0 4.5-3 8.6-7 9.7-4-1.1-7-5.2-7-9.7V6.2L12 3z" />
                        </svg>
                        Admin
                    </a>
                    <a href="{{ route('guru.login') }}"
                        class="flex flex-1 items-center justify-center gap-1.5 rounded-full py-2.5 text-center transition
                        {{ request()->routeIs('guru.login') ? 'bg-brand-600 text-white shadow-button' : 'text-slate-500 hover:bg-white hover:text-brand-700' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 14l8-4-8-4-8 4 8 4zm0 0v6m-6-4.2V17a6 3 0 0012 0v-1.2" />
                        </svg>
                        Guru
                    </a>
                    <a href="{{ route('siswa.login') }}"
                        class="flex flex-1 items-center justify-center gap-1.5 rounded-full py-2.5 text-center transition
                        {{ request()->routeIs('siswa.login') ? 'bg-brand-600 text-white shadow-button' : 'text-slate-500 hover:bg-white hover:text-brand-700' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 14a4 4 0 100-8 4 4 0 000 8zm-7 7a7 7 0 0114 0" />
                        </svg>
                        Siswa
                    </a>
                </div>

                <h1 class="font-display text-2xl font-extrabold tracking-[-0.03em] text-ink">@yield('heading')</h1>
                <p class="mt-1.5 text-sm text-slate-500">@yield('subheading')</p>

                @if($errors->any())
                    <div
                        class="mt-5 flex items-start gap-2.5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="2">
                            <circle cx="12" cy="12" r="9" stroke-linecap="round" stroke-linejoin="round" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v5m0 3h.01" />
                        </svg>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                @yield('form')
            </div>

            <a href="{{ url('/') }}"
                class="group mt-7 flex items-center justify-center gap-1.5 text-sm font-medium text-slate-500 transition hover:text-brand-600">
                <svg class="h-4 w-4 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke beranda
            </a>
        </div>
    </section>

</body>

</html>