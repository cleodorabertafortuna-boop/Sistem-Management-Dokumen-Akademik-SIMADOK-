<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#2454C7">

    <title>SIMADOK — SMK N 5 Surakarta</title>

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
        html {
            scroll-behavior: smooth;
            scroll-padding-top: 5rem;
        }

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

        .section-title {
            font-size: clamp(1.75rem, 1.3rem + 1.8vw, 2.75rem);
            line-height: 1.12;
            letter-spacing: -0.03em;
        }

        /* ---------- Teks gradien ---------- */
        .grad-text {
            background: linear-gradient(100deg, #1E45AA 0%, #0EA5B7 45%, #2454C7 90%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        /* ---------- Grid latar ---------- */
        .grid-bg {
            background-image:
                linear-gradient(to right, rgba(36, 84, 199, .06) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(36, 84, 199, .06) 1px, transparent 1px);
            background-size: 44px 44px;
            -webkit-mask-image: radial-gradient(ellipse 70% 55% at 50% 0%, #000 30%, transparent 75%);
            mask-image: radial-gradient(ellipse 70% 55% at 50% 0%, #000 30%, transparent 75%);
        }

        /* ---------- Animasi masuk ---------- */
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

        /* Muncul saat di-scroll */
        .reveal {
            opacity: 0;
            transform: translateY(14px);
            transition: opacity .5s ease, transform .5s ease;
            transition-delay: var(--d, 0s);
        }

        .reveal.is-visible {
            opacity: 1;
            transform: none;
        }

        .document-back-left {
            transform: rotate(-8deg) translateX(-34px);
        }

        .document-back-right {
            transform: rotate(6deg) translateX(34px);
        }

        /* ---------- Alur ---------- */
        .alur-line-v {
            background-image: repeating-linear-gradient(to bottom, #C3D5FF 0, #C3D5FF 8px, transparent 8px, transparent 16px);
        }

        .alur-line-h {
            background-image: repeating-linear-gradient(to right, #C3D5FF 0, #C3D5FF 8px, transparent 8px, transparent 16px);
        }

        /* ---------- Menu mobile ---------- */
        #mobile-menu {
            transition: max-height .35s ease, opacity .25s ease;
            max-height: 0;
            opacity: 0;
            overflow: hidden;
        }

        #mobile-menu[data-open="true"] {
            max-height: 360px;
            opacity: 1;
        }

        /* ---------- Kurangi gerakan ---------- */
        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }

            .reveal,
            .rise-in {
                opacity: 1;
                transform: none;
                transition: none;
                animation: none !important;
            }
        }
    </style>

    <noscript>
        <style>
            .reveal {
                opacity: 1 !important;
                transform: none !important;
            }
        </style>
    </noscript>
</head>


<body class="min-h-screen bg-[#F7FAFF] text-slate-800 antialiased">


    {{-- =========================================================
    BACKGROUND
    ========================================================== --}}
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -left-32 -top-32 h-[30rem] w-[30rem] rounded-full bg-brand-300/35 blur-[90px]"></div>
        <div class="absolute -right-32 top-[30%] h-[28rem] w-[28rem] rounded-full bg-teal-500/25 blur-[90px]"></div>
        <div class="absolute bottom-[-8rem] left-[25%] h-[25rem] w-[25rem] rounded-full bg-amber-500/15 blur-[90px]">
        </div>
        <div class="grid-bg absolute inset-0"></div>
    </div>


    {{-- =========================================================
    NAVBAR
    ========================================================== --}}
    <header id="navbar" class="sticky top-0 z-50 border-b border-transparent transition-all duration-300">
        <nav class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3.5 sm:px-6">

            <a href="#top" class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center sm:h-11 sm:w-11">
                    <img src="{{ asset('images/logo-sekolah.png') }}" alt="Logo SMK Negeri 5 Surakarta"
                        class="h-10 w-10 object-contain sm:h-11 sm:w-11">
                </div>
                <div class="leading-tight">
                    <div class="font-display text-base font-extrabold tracking-tight text-ink">SIMADOK</div>
                    <div class="mt-0.5 text-[11px] font-medium text-slate-500">SMK N 5 Surakarta</div>
                </div>
            </a>

            {{-- Menu desktop --}}
            <div class="hidden items-center gap-1 text-sm font-medium text-slate-600 md:flex">
                <a href="#jenis-dokumen" data-nav
                    class="rounded-lg px-3.5 py-2 transition hover:bg-brand-50 hover:text-brand-700">Jenis dokumen</a>
                <a href="#alur" data-nav
                    class="rounded-lg px-3.5 py-2 transition hover:bg-brand-50 hover:text-brand-700">Alur verifikasi</a>
                <a href="#keunggulan" data-nav
                    class="rounded-lg px-3.5 py-2 transition hover:bg-brand-50 hover:text-brand-700">Keunggulan</a>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.login') }}"
                    class="group hidden items-center gap-2 rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-button transition hover:-translate-y-0.5 hover:bg-brand-700 sm:inline-flex">
                    Masuk ke sistem
                    <svg class="h-4 w-4 transition-transform group-hover:translate-x-0.5" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>

                {{-- Tombol menu mobile --}}
                <button id="menu-toggle" type="button" aria-label="Buka menu" aria-expanded="false"
                    aria-controls="mobile-menu"
                    class="flex h-10 w-10 items-center justify-center rounded-xl border border-brand-100 bg-white/80 text-ink md:hidden">
                    <svg id="icon-burger" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 7h16M4 12h16M4 17h16" />
                    </svg>
                    <svg id="icon-close" class="hidden h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </nav>

        <div id="mobile-menu" data-open="false" class="border-t border-white/70 bg-white/90 backdrop-blur-xl md:hidden">
            <div class="mx-auto flex max-w-6xl flex-col gap-1 px-4 py-3 text-sm font-medium text-slate-700 sm:px-6">
                <a href="#jenis-dokumen" data-close-menu class="rounded-lg px-3 py-3 hover:bg-brand-50">Jenis
                    dokumen</a>
                <a href="#alur" data-close-menu class="rounded-lg px-3 py-3 hover:bg-brand-50">Alur verifikasi</a>
                <a href="#keunggulan" data-close-menu class="rounded-lg px-3 py-3 hover:bg-brand-50">Keunggulan</a>
                <a href="{{ route('admin.login') }}"
                    class="mt-2 inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 py-3 font-semibold text-white shadow-button">
                    Masuk ke sistem
                </a>
            </div>
        </div>
    </header>


    <main id="top">

        {{-- =========================================================
        HERO
        ========================================================== --}}
        <section
            class="mx-auto grid max-w-6xl items-center gap-10 px-4 pb-14 pt-10 sm:px-6 sm:pt-16 lg:grid-cols-[1.05fr_.95fr] lg:gap-14 lg:pb-24 lg:pt-20">

            <div class="text-center lg:text-left">

                <h1
                    class="rise-in mx-auto max-w-3xl font-display text-[2.15rem] font-extrabold leading-[1.1] tracking-[-0.035em] text-ink sm:text-5xl lg:mx-0 lg:text-[3.75rem]">
                    Dokumen akademik siswa,
                    <span class="grad-text">rapi dalam satu sistem.</span>
                </h1>

                <p
                    class="rise-in mx-auto mt-5 max-w-xl text-base leading-7 text-slate-600 sm:mt-6 sm:text-lg sm:leading-8 lg:mx-0">
                    SIMADOK menyimpan dan mengelola raport, ijazah, dan sertifikat siswa
                    SMK Negeri 5 Surakarta secara digital — terpusat, aman, dan mudah
                    ditelusuri kapan saja.
                </p>

                <div
                    class="rise-in mt-8 flex flex-col items-stretch gap-3 sm:flex-row sm:justify-center lg:justify-start">
                    <a href="{{ route('admin.login') }}"
                        class="group inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-6 py-3.5 text-sm font-semibold text-white shadow-button transition hover:-translate-y-0.5 hover:bg-brand-700 active:translate-y-0">
                        Masuk
                        <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                </div>
            </div>


            {{-- Ilustrasi dokumen --}}
            <div
                class="rise-in relative flex min-h-[340px] items-center justify-center sm:min-h-[420px] lg:min-h-[500px]">

                <div class="absolute h-64 w-64 rounded-full bg-brand-300/30 blur-3xl sm:h-80 sm:w-80"></div>

                <div class="relative h-[320px] w-[250px] sm:h-[390px] sm:w-[330px]">

                    <div
                        class="document-back-left absolute left-1/2 top-1/2 h-[270px] w-[195px] -translate-x-1/2 -translate-y-1/2 rounded-[22px] border border-brand-100 bg-white/90 shadow-card sm:h-[335px] sm:w-[250px]">
                    </div>
                    <div
                        class="document-back-right absolute left-1/2 top-1/2 h-[270px] w-[195px] -translate-x-1/2 -translate-y-1/2 rounded-[22px] border border-brand-100 bg-white/90 shadow-card sm:h-[335px] sm:w-[250px]">
                    </div>

                    <div
                        class="absolute left-1/2 top-1/2 h-[290px] w-[210px] -translate-x-1/2 -translate-y-1/2 overflow-hidden rounded-[22px] border border-brand-100 bg-white p-5 shadow-[0_25px_70px_rgba(15,46,90,.18)] sm:h-[355px] sm:w-[265px] sm:p-6">

                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="h-2.5 w-24 rounded-full bg-slate-200"></div>
                                <div class="mt-2 h-2 w-16 rounded-full bg-slate-100"></div>
                            </div>
                        </div>

                        <div class="mt-7 space-y-3">
                            <div class="h-2 w-full rounded-full bg-slate-100"></div>
                            <div class="h-2 w-[88%] rounded-full bg-slate-100"></div>
                            <div class="h-2 w-[70%] rounded-full bg-slate-100"></div>
                            <div class="mt-6 h-14 w-full rounded-xl bg-gradient-to-r from-brand-50 to-teal-50 sm:h-16">
                            </div>
                            <div class="h-2 w-[92%] rounded-full bg-slate-100"></div>
                            <div class="h-2 w-[65%] rounded-full bg-slate-100"></div>
                        </div>

                        <div
                            class="absolute bottom-5 left-5 right-5 flex items-center justify-between sm:bottom-6 sm:left-6 sm:right-6">
                            <span
                                class="inline-flex items-center gap-1.5 rounded-lg bg-teal-50 px-2.5 py-1.5 text-[11px] font-semibold text-teal-700">
                                <span class="h-1.5 w-1.5 rounded-full bg-teal-500"></span>
                                Terverifikasi
                            </span>
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-50 text-brand-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        {{-- =========================================================
        DATA JENIS DOKUMEN
        ========================================================== --}}
        @php
            $jenisDokumen = [
                [
                    'label' => 'Raport',
                    'desc' => 'Rekap nilai semester tiap siswa, tersusun per tahun ajaran.',
                    'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                    'tone' => 'brand'
                ],
                [
                    'label' => 'Ijazah',
                    'desc' => 'Dokumen kelulusan resmi yang tersimpan dan mudah diverifikasi ulang.',
                    'icon' => 'M12 14l9-5-9-5-9 5 9 5zm0 0v7m0-7l-9-5m9 5l9-5',
                    'tone' => 'teal'
                ],
                [
                    'label' => 'Sertifikat TOEIC',
                    'desc' => 'Bukti kemampuan bahasa Inggris siswa dari uji kompetensi resmi.',
                    'icon' => 'M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01',
                    'tone' => 'amber'
                ],
                [
                    'label' => 'Sertifikat TKA',
                    'desc' => 'Hasil Tes Kemampuan Akademik sebagai penunjang kelulusan.',
                    'icon' => 'M9 17v-2a4 4 0 014-4h4M9 17H7a2 2 0 01-2-2V7a2 2 0 012-2h10a2 2 0 012 2v2',
                    'tone' => 'brand'
                ],
                [
                    'label' => 'UKK',
                    'desc' => 'Uji Kompetensi Keahlian tiap jurusan, terarsip lengkap per angkatan.',
                    'icon' => 'M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z',
                    'tone' => 'teal'
                ],
                [
                    'label' => 'PKL',
                    'desc' => 'Laporan dan sertifikat Praktik Kerja Lapangan dari mitra industri.',
                    'icon' => 'M3 7l9-4 9 4-9 4-9-4zm0 6l9 4 9-4M3 13v4l9 4 9-4v-4',
                    'tone' => 'amber'
                ],
            ];

            $tone = [
                'brand' => ['bg' => 'bg-brand-50', 'text' => 'text-brand-600', 'hover' => 'group-hover:bg-brand-600'],
                'teal' => ['bg' => 'bg-teal-50', 'text' => 'text-teal-600', 'hover' => 'group-hover:bg-teal-600'],
                'amber' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'hover' => 'group-hover:bg-amber-500'],
            ];
        @endphp


        {{-- =========================================================
        JENIS DOKUMEN
        ========================================================== --}}
        <section id="jenis-dokumen" class="mx-auto max-w-6xl px-4 py-16 sm:px-6 sm:py-24">

            <div class="reveal mb-10 max-w-xl sm:mb-12">
                <p class="mb-2 text-xs font-bold uppercase tracking-[0.14em] text-brand-500">Jenis dokumen</p>
                <h2 class="section-title font-extrabold text-ink">Enam jenis dokumen, satu tempat penyimpanan</h2>
                <p class="mt-3 text-slate-600">
                    Setiap kategori punya alur pengarsipan sendiri, tetapi semuanya bisa
                    dicari dan diunduh dari dasbor yang sama.
                </p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 lg:gap-5">
                @foreach ($jenisDokumen as $i => $d)
                    @php $t = $tone[$d['tone']]; @endphp
                    <div class="reveal group rounded-2xl border border-brand-100 bg-white/75 p-5 backdrop-blur transition duration-300 hover:-translate-y-0.5 hover:border-brand-200 hover:bg-white hover:shadow-card sm:p-6"
                        style="--d: {{ ($i % 3) * 0.08 }}s">
                        <div
                            class="relative flex h-12 w-12 items-center justify-center rounded-xl {{ $t['bg'] }} {{ $t['text'] }} transition duration-300 {{ $t['hover'] }} group-hover:text-white">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="{{ $d['icon'] }}" />
                            </svg>
                        </div>
                        <h3 class="relative mt-4 text-base font-bold text-ink">{{ $d['label'] }}</h3>
                        <p class="relative mt-1.5 text-sm leading-6 text-slate-500">{{ $d['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>


        {{-- =========================================================
        ALUR VERIFIKASI
        ========================================================== --}}
        @php
            $alur = [
                ['title' => 'Unggah dokumen', 'desc' => 'Wali kelas atau staf tata usaha mengunggah dokumen ke sistem.'],
                ['title' => 'Verifikasi data', 'desc' => 'Admin memeriksa kelengkapan dan kecocokan data siswa.'],
                ['title' => 'Tanda tangan digital', 'desc' => 'Kepala sekolah atau pejabat berwenang mengesahkan dokumen.'],
                ['title' => 'Arsip & akses', 'desc' => 'Dokumen tersimpan permanen dan siap diunduh saat dibutuhkan.'],
            ];
        @endphp

        <section id="alur" class="border-y border-brand-100/60 bg-white/50 backdrop-blur">
            <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 sm:py-24">

                <div class="reveal mb-12 max-w-xl">
                    <p class="mb-2 text-xs font-bold uppercase tracking-[0.14em] text-brand-500">Alur verifikasi</p>
                    <h2 class="section-title font-extrabold text-ink">Dari unggah sampai terarsip</h2>
                    <p class="mt-3 text-slate-600">
                        Empat tahap yang sama untuk setiap dokumen, sehingga statusnya
                        selalu jelas bagi siapa pun yang mengaksesnya.
                    </p>
                </div>

                <ol class="grid gap-0 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($alur as $i => $step)
                        <li class="reveal relative flex gap-4 pb-8 sm:block sm:pb-0 lg:px-5 lg:first:pl-0 lg:last:pr-0"
                            style="--d: {{ $i * 0.1 }}s">

                            {{-- garis penghubung: vertikal di mobile, horizontal di desktop --}}
                            @if (!$loop->last)
                                <div class="alur-line-v absolute left-6 top-14 h-[calc(100%-3.25rem)] w-px sm:hidden"></div>
                                <div class="alur-line-h absolute right-[-1rem] top-6 hidden h-px w-8 lg:block"></div>
                            @endif

                            <div
                                class="relative z-10 flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-ink to-brand-600 font-display text-base font-bold text-white shadow-button transition sm:mb-0">
                                {{ $i + 1 }}
                            </div>

                            <div class="sm:mt-4">
                                <h3 class="text-base font-bold text-ink">{{ $step['title'] }}</h3>
                                <p class="mt-1.5 max-w-xs text-sm leading-6 text-slate-500">{{ $step['desc'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>


        {{-- =========================================================
        KEUNGGULAN
        ========================================================== --}}
        <section id="keunggulan" class="mx-auto max-w-6xl px-4 py-16 sm:px-6 sm:py-24">

            <div class="reveal mb-10 max-w-xl sm:mb-12">
                <p class="mb-2 text-xs font-bold uppercase tracking-[0.14em] text-brand-500">Keunggulan</p>
                <h2 class="section-title font-extrabold text-ink">Dibuat supaya urusan dokumen jadi ringan</h2>
            </div>

            <div class="grid gap-4 md:grid-cols-3 lg:gap-5">

                <div
                    class="reveal group flex items-start gap-4 rounded-2xl border border-brand-100 bg-white/75 p-5 transition duration-300 hover:-translate-y-0.5 hover:bg-white hover:shadow-card">
                    <div
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600 transition group-hover:bg-brand-600 group-hover:text-white">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="relative">
                        <p class="text-base font-bold text-ink">Terpusat</p>
                        <p class="mt-1 text-sm leading-6 text-slate-500">Semua dokumen dalam satu sistem, tidak lagi
                            tersebar di banyak map dan folder.</p>
                    </div>
                </div>

                <div class="reveal group flex items-start gap-4 rounded-2xl border border-brand-100 bg-white/75 p-5 transition duration-300 hover:-translate-y-0.5 hover:bg-white hover:shadow-card"
                    style="--d:.1s">
                    <div
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-teal-50 text-teal-600 transition group-hover:bg-teal-600 group-hover:text-white">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-5a2 2 0 00-2-2H6a2 2 0 00-2 2v5a2 2 0 002 2zm10-9V7a4 4 0 00-8 0v3h8z" />
                        </svg>
                    </div>
                    <div class="relative">
                        <p class="text-base font-bold text-ink">Aman</p>
                        <p class="mt-1 text-sm leading-6 text-slate-500">Data dikelola secara terstruktur, dengan hak
                            akses berbeda untuk tiap peran.</p>
                    </div>
                </div>

                <div class="reveal group flex items-start gap-4 rounded-2xl border border-brand-100 bg-white/75 p-5 transition duration-300 hover:-translate-y-0.5 hover:bg-white hover:shadow-card"
                    style="--d:.2s">
                    <div
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600 transition group-hover:bg-amber-500 group-hover:text-white">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="relative">
                        <p class="text-base font-bold text-ink">Mudah ditelusuri</p>
                        <p class="mt-1 text-sm leading-6 text-slate-500">Akses dokumen dengan lebih cepat lewat
                            pencarian dan filter di dasbor.</p>
                    </div>
                </div>

            </div>
        </section>


        {{-- =========================================================
        CTA
        ========================================================== --}}
        <section class="mx-auto max-w-6xl px-4 pb-16 sm:px-6 sm:pb-24">
            <div
                class="reveal relative overflow-hidden rounded-3xl bg-gradient-to-br from-ink via-brand-700 to-brand-500 px-6 py-12 text-center shadow-card sm:px-12 sm:py-16">
                <div
                    class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-teal-500/30 blur-3xl">
                </div>
                <div
                    class="pointer-events-none absolute -bottom-20 -left-10 h-64 w-64 rounded-full bg-amber-500/20 blur-3xl">
                </div>

                <h2
                    class="relative mx-auto max-w-2xl font-display text-2xl font-extrabold leading-tight tracking-tight text-white sm:text-4xl">
                    Siap merapikan dokumen akademik sekolah?
                </h2>
                <p class="relative mx-auto mt-3 max-w-lg text-sm leading-7 text-brand-100 sm:text-base">
                    Masuk ke SIMADOK untuk mengunggah, memverifikasi, dan mengarsipkan dokumen siswa.
                </p>
                <a href="{{ route('admin.login') }}"
                    class="group relative mt-7 inline-flex items-center justify-center gap-2 rounded-xl bg-white px-6 py-3.5 text-sm font-bold text-brand-700 shadow-soft transition hover:-translate-y-0.5 hover:bg-brand-50">
                    Masuk ke SIMADOK
                    <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
            </div>
        </section>

    </main>


    {{-- =========================================================
    FOOTER
    ========================================================== --}}
    <footer class="border-t border-brand-100/70 bg-white/60 backdrop-blur">
        <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6">

            <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">

                <div>
                    <p class="font-display text-sm font-bold text-ink">SIMADOK</p>
                    <p class="mt-1 max-w-xs text-xs leading-5 text-slate-500">
                        Sistem Manajemen Dokumen Akademik — SMK Negeri 5 Surakarta.
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold text-ink">Tautan</p>
                    <ul class="mt-3 space-y-2 text-xs text-slate-500">
                        <li><a href="#jenis-dokumen" class="transition hover:text-brand-700">Jenis dokumen</a></li>
                        <li><a href="#alur" class="transition hover:text-brand-700">Alur verifikasi</a></li>
                        <li><a href="#keunggulan" class="transition hover:text-brand-700">Keunggulan</a></li>
                        <li><a href="{{ route('admin.login') }}" class="transition hover:text-brand-700">Masuk ke
                                sistem</a></li>
                    </ul>
                </div>

                <div>
                    <p class="text-xs font-semibold text-ink">Kontak</p>
                    <ul class="mt-3 space-y-2 text-xs text-slate-500">
                        <li>Jl. Dr. Supomo No. 25, Surakarta</li>
                        <li>tu@smkn5surakarta.sch.id</li>
                        <li>(0271) 000-0000</li>
                    </ul>
                </div>

                <div>
                    <p class="text-xs font-semibold text-ink">Jam layanan</p>
                    <ul class="mt-3 space-y-2 text-xs text-slate-500">
                        <li>Senin – Jumat, 07.30 – 15.00</li>
                        <li>Sabtu – Minggu, tutup</li>
                    </ul>
                </div>

            </div>

            <div
                class="mt-10 flex flex-col items-center justify-between gap-3 border-t border-brand-100/70 pt-6 text-center sm:flex-row sm:text-left">
                <p class="text-xs text-slate-400">&copy; {{ date('Y') }} SIMADOK — SMK N 5 Surakarta</p>
                <a href="#top"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-600 transition hover:text-brand-700">
                    Kembali ke atas
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                    </svg>
                </a>
            </div>

        </div>
    </footer>


    {{-- =========================================================
    SCRIPTS
    ========================================================== --}}
    <script>
            (function () {
                var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

                /* ---- Navbar: kaca saat di-scroll ---- */
                var navbar = document.getElementById('navbar');

                function onScroll() {
                    var y = window.scrollY || document.documentElement.scrollTop;

                    if (y > 12) {
                        navbar.classList.add('bg-white/80', 'backdrop-blur-xl', 'border-white/70', 'shadow-sm');
                        navbar.classList.remove('border-transparent');
                    } else {
                        navbar.classList.remove('bg-white/80', 'backdrop-blur-xl', 'border-white/70', 'shadow-sm');
                        navbar.classList.add('border-transparent');
                    }
                }
                window.addEventListener('scroll', onScroll, { passive: true });
                onScroll();

                /* ---- Menu mobile ---- */
                var toggle = document.getElementById('menu-toggle');
                var menu = document.getElementById('mobile-menu');
                var iconBurger = document.getElementById('icon-burger');
                var iconClose = document.getElementById('icon-close');

                function setMenu(open) {
                    menu.setAttribute('data-open', open ? 'true' : 'false');
                    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                    iconBurger.classList.toggle('hidden', open);
                    iconClose.classList.toggle('hidden', !open);
                }
                toggle.addEventListener('click', function () {
                    setMenu(menu.getAttribute('data-open') !== 'true');
                });
                document.querySelectorAll('[data-close-menu]').forEach(function (a) {
                    a.addEventListener('click', function () { setMenu(false); });
                });
                window.addEventListener('resize', function () {
                    if (window.innerWidth >= 768) setMenu(false);
                });
                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape') setMenu(false);
                });

                /* ---- Muncul saat di-scroll ---- */
                var reveals = document.querySelectorAll('.reveal');
                if ('IntersectionObserver' in window && !reduce) {
                    var io = new IntersectionObserver(function (entries) {
                        entries.forEach(function (en) {
                            if (en.isIntersecting) {
                                en.target.classList.add('is-visible');
                                io.unobserve(en.target);
                            }
                        });
                    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
                    reveals.forEach(function (el) { io.observe(el); });
                } else {
                    reveals.forEach(function (el) { el.classList.add('is-visible'); });
                }

                /* ---- Tandai menu aktif sesuai bagian yang terlihat ---- */
                var links = document.querySelectorAll('[data-nav]');
                var sections = Array.prototype.map.call(links, function (a) {
                    return document.querySelector(a.getAttribute('href'));
                });
                if ('IntersectionObserver' in window) {
                    var spy = new IntersectionObserver(function (entries) {
                        entries.forEach(function (en) {
                            if (!en.isIntersecting) return;
                            links.forEach(function (a) {
                                var on = a.getAttribute('href') === '#' + en.target.id;
                                a.classList.toggle('bg-brand-50', on);
                                a.classList.toggle('text-brand-700', on);
                            });
                        });
                    }, { rootMargin: '-45% 0px -50% 0px' });
                    sections.forEach(function (s) { if (s) spy.observe(s); });
                }
            })();
    </script>

</body>

</html>