<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Faskes BPJS KC Bukittinggi</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#0052A5',
                            light: '#1a6fc4',
                            dark: '#003d7a'
                        },
                        surface: '#F4F7FB',
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif']
                    },
                }
            }
        }
    </script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* ── Leaflet overrides ── */
        .leaflet-tooltip {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 12px;
            font-weight: 600;
            background: #0052A5;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 4px 10px;
            box-shadow: 0 4px 12px rgba(0, 82, 165, 0.3);
        }

        .leaflet-tooltip::before {
            border-top-color: #0052A5;
        }

        .leaflet-control-zoom {
            border: none !important;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12) !important;
        }

        .leaflet-control-zoom a {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            color: #0052A5 !important;
            font-weight: 700 !important;
            border-radius: 8px !important;
        }

        /* ── Sidebar scrollbar ── */
        #sidebar-content::-webkit-scrollbar {
            width: 4px;
        }

        #sidebar-content::-webkit-scrollbar-track {
            background: transparent;
        }

        #sidebar-content::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 99px;
        }

        /* ── Animations ── */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .fade-in {
            animation: fadeIn 0.25s ease forwards;
        }

        .slide-in {
            animation: slideIn 0.2s ease forwards;
        }

        .kabkota-card {
            animation: fadeIn 0.3s ease forwards;
            opacity: 0;
        }

        .kabkota-card:nth-child(1) {
            animation-delay: 0.05s;
        }

        .kabkota-card:nth-child(2) {
            animation-delay: 0.1s;
        }

        .kabkota-card:nth-child(3) {
            animation-delay: 0.15s;
        }

        .kabkota-card:nth-child(4) {
            animation-delay: 0.2s;
        }

        .kabkota-card:nth-child(5) {
            animation-delay: 0.25s;
        }

        .faskes-item {
            animation: slideIn 0.2s ease forwards;
            opacity: 0;
        }

        /* ── Loading spinner ── */
        .spinner {
            width: 18px;
            height: 18px;
            border: 2px solid #e2e8f0;
            border-top-color: #0052A5;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            display: inline-block;
        }

        /* ── Map full height ── */
        #map {
            position: absolute;
            inset: 0;
            z-index: 1;
        }

        /* ── Mobile bottom sheet ── */
        @media (max-width: 768px) {
            #sidebar {
                position: fixed !important;
                bottom: 0;
                left: 0;
                right: 0;
                width: 100% !important;
                border-radius: 20px 20px 0 0;
                z-index: 1000;
                box-shadow: 0 -8px 32px rgba(0, 0, 0, 0.15);
                will-change: height;
                /* ← tambah ini */
                transform: translateZ(0);
                /* ← dan ini, force GPU */
            }

            #sidebar-content {
                max-height: 50vh;
            }

            #map-container {
                position: fixed !important;
                inset: 0 !important;
                height: 100vh !important;
                width: 100vw !important;
                z-index: 1 !important;
            }

            .drag-handle {
                display: flex !important;
            }
        }

        .drag-handle {
            display: none;
        }

        /* ── Modal backdrop blur ── */
        .modal-backdrop {
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }

        /* ── Pulse dot ── */
        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.5;
                transform: scale(1.3);
            }
        }

        .pulse-dot {
            animation: pulse 1.5s ease infinite;
        }
    </style>
</head>

<body class="bg-surface overflow-hidden">

    <div class="relative w-screen h-screen flex flex-col md:flex-row">

        {{-- ── MAP CONTAINER ── --}}
        <div id="map-container" class="flex-1 relative">
            <div id="map"></div>

            {{-- Loading overlay --}}
            <div id="loading"
                class="absolute inset-0 z-50 flex items-center justify-center bg-white/80 backdrop-blur-sm">
                <div class="flex flex-col items-center gap-3">
                    <div class="w-10 h-10 border-4 border-primary/20 border-t-primary rounded-full"
                        style="animation: spin 0.8s linear infinite"></div>
                    <p class="text-sm font-semibold text-primary">Memuat peta...</p>
                </div>
            </div>

            {{-- Map attribution badge --}}
            <div class="absolute bottom-6 right-4 z-10 hidden md:block">
                <div
                    class="bg-white/90 backdrop-blur-sm rounded-lg px-3 py-1.5 text-xs text-slate-500 shadow-sm border border-white">
                    © OpenStreetMap contributors
                </div>
            </div>
        </div>

        {{-- ── SIDEBAR ── --}}
        <div id="sidebar"
            class="w-full md:w-80 lg:w-96 bg-white flex flex-col md:relative md:h-screen shadow-2xl md:shadow-xl order-2 md:order-1"
            style="z-index:1000">

            {{-- Drag handle (mobile) --}}
            {{-- <div class="drag-handle justify-center pt-3 pb-1"> --}}
            <div class="drag-handle justify-center pt-3 pb-2 cursor-grab active:cursor-grabbing select-none">
                <div class="w-10 h-1 bg-slate-200 rounded-full"></div>
            </div>

            {{-- Header --}}
            <div
                class="bg-gradient-to-br from-primary to-primary-dark px-5 py-4 flex-shrink-0 relative overflow-hidden">
                {{-- Background decoration --}}
                <div class="absolute -top-6 -right-6 w-24 h-24 bg-white/5 rounded-full"></div>
                <div class="absolute -bottom-4 -left-4 w-16 h-16 bg-white/5 rounded-full"></div>

                <div class="relative flex items-start gap-3">
                    <div class="w-10 h-10 bg-white/15 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-white font-bold text-base leading-tight">Peta Faskes BPJS</h1>
                        <p class="text-blue-200 text-xs mt-0.5">KC Bukittinggi · 5 Kabupaten/Kota</p>
                    </div>
                </div>
            </div>

            {{-- Search Box --}}
            <div id="search-box" class="px-4 pt-3 pb-2 border-b border-slate-100 flex-shrink-0">
                <div class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                    </svg>
                    <input id="search-input" type="text" autofocus placeholder="Cari nama faskes..."
                        class="w-full pl-9 pr-9 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl
                                focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary
                                text-slate-700 placeholder-slate-400 transition-all"/>
                    <button id="search-clear"
                            class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Content --}}
            <div id="sidebar-content" class="flex-1 overflow-y-auto">

                {{-- ── Panel: List Kab/Kota ── --}}
                <div id="list-panel" class="p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-1 h-4 bg-primary rounded-full"></div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pilih Wilayah</p>
                    </div>
                    <div id="kabkota-list" class="space-y-2">
                        {{-- Loading state --}}
                        @foreach (range(1, 5) as $i)
                            <div class="h-16 bg-slate-100 rounded-xl animate-pulse"></div>
                        @endforeach
                    </div>
                </div>

                {{-- ── Panel: Detail Kab/Kota ── --}}
                <div id="detail-panel" class="hidden p-4">
                    <button onclick="backToList()"
                        class="flex items-center gap-1.5 text-primary text-xs font-semibold mb-4 hover:gap-2.5 transition-all group">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Semua Wilayah
                    </button>

                    <div class="bg-primary/5 border border-primary/10 rounded-xl p-3.5 mb-4">
                        <h2 class="font-bold text-primary text-sm" id="detail-title"></h2>
                        <p class="text-xs text-slate-400 mt-0.5" id="detail-populasi"></p>  <!-- ← tambah -->
                        <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Klik kecamatan di peta untuk melihat faskes
                        </p>
                    </div>

                    <div id="fkrtl-list" class="space-y-2"></div>
                </div>

                {{-- ── Panel: Kecamatan ── --}}
                <div id="kecamatan-panel" class="hidden p-4">
                    <button onclick="backToKabkota()"
                        class="flex items-center gap-1.5 text-primary text-xs font-semibold mb-4 hover:gap-2.5 transition-all group">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali
                    </button>

                    <div class="flex items-center gap-2.5 mb-4">
                        <div class="w-8 h-8 bg-violet-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-violet-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-medium">Kecamatan</p>
                            <h2 class="font-bold text-slate-800 text-sm leading-tight" id="kecamatan-title"></h2>
                            <p class="text-xs text-slate-400 mt-0.5" id="kecamatan-populasi"></p>
                        </div>
                    </div>

                    <div id="kecamatan-faskes" class="space-y-2"></div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="flex-shrink-0 px-4 py-3 border-t border-slate-100 bg-slate-50/50">
                <p class="text-center text-xs text-slate-400">BPJS Kesehatan · KC Bukittinggi</p>
            </div>
        </div>
    </div>

    {{-- ── MODAL ── --}}
    <div id="modalOverlay" onclick="closeModal(event)"
        class="modal-backdrop hidden fixed inset-0 bg-black/50 z-[9999] items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-md max-h-[85vh] overflow-hidden flex flex-col shadow-2xl"
            onclick="event.stopPropagation()">

            {{-- Modal header --}}
            <div class="bg-gradient-to-br from-primary to-primary-dark p-5 relative flex-shrink-0">
                <button onclick="closeModalDirect()"
                    class="absolute top-4 right-4 w-8 h-8 bg-white/15 hover:bg-white/25 rounded-lg flex items-center justify-center transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div id="modal-tipe-badge" class="mb-2"></div>
                <h3 id="modal-nama" class="text-white font-bold text-base pr-10 leading-snug"></h3>
                <p id="modal-subtitle" class="text-blue-200 text-xs mt-1.5 flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    </svg>
                    <span></span>
                </p>
            </div>

            {{-- Modal body --}}
            <div class="overflow-y-auto flex-1 p-5">

                {{-- Info grid --}}
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-xs text-slate-400 font-medium mb-1">Jenis Faskes</p>
                        <p id="modal-tipe-detail" class="text-sm font-bold text-slate-700"></p>
                    </div>

                    <div id="modal-kelas-container" class="bg-slate-50 rounded-xl p-3">
                        <p class="text-xs text-slate-400 font-medium mb-1">Kelas</p>
                        <p id="modal-kelas" class="text-sm font-bold text-slate-700"></p>
                    </div>

                    <div id="modal-tipe-fktp-container" class="bg-slate-50 rounded-xl p-3 hidden">
                        <p class="text-xs text-slate-400 font-medium mb-1">Tipe FKTP</p>
                        <p id="modal-tipe-fktp-badge" class="text-sm font-bold text-slate-700"></p>
                    </div>

                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-xs text-slate-400 font-medium mb-1">Status</p>
                        <p id="modal-status-badge" class="text-sm font-bold text-slate-700"></p>
                    </div>
                </div>

                {{-- <div class="mb-4 flex items-center gap-2">
                    <div id="modal-status-badge"></div>
                    <div id="modal-tipe-fktp-badge"></div>
                </div> --}}

                {{-- Contact info --}}
                <div class="space-y-3 mb-4">
                    <div id="modal-alamat-row" class="flex gap-3 items-start">
                        <div
                            class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-medium">Alamat</p>
                            <p id="modal-alamat" class="text-sm text-slate-700 font-medium mt-0.5"></p>
                        </div>
                    </div>

                    <div id="modal-telepon-row" class="flex gap-3 items-center">
                        <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-medium">Telepon</p>
                            <p id="modal-telepon" class="text-sm text-slate-700 font-medium mt-0.5"></p>
                        </div>
                    </div>

                    <div id="modal-email-row" class="flex gap-3 items-center">
                        <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-medium">Email</p>
                            <p id="modal-email" class="text-sm text-slate-700 font-medium mt-0.5"></p>
                        </div>
                    </div>

                    <div id="modal-website-row" class="flex gap-3 items-center">
                        <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-medium">Website</p>
                            <p id="modal-website" class="text-sm text-slate-700 font-medium mt-0.5"></p>
                        </div>
                    </div>
                </div>

                {{-- Peserta Terdaftar (FKTP only) --}}
                <div id="modal-peserta-wrap" class="hidden">
                    <div class="border-t border-slate-100 pt-4">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Jumlah Peserta Terdaftar
                        </p>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="bg-blue-50 rounded-xl p-3">
                                <p class="text-xs text-slate-400 font-medium mb-1">Peserta Terdaftar</p>
                                <p id="modal-peserta-terdaftar" class="text-sm font-bold text-slate-700">-</p>
                            </div>
                            <div class="bg-blue-50 rounded-xl p-3">
                                <p class="text-xs text-slate-400 font-medium mb-1">Prolanis DM</p>
                                <p id="modal-prolanis-dm" class="text-sm font-bold text-slate-700">-</p>
                            </div>
                            <div class="bg-blue-50 rounded-xl p-3">
                                <p class="text-xs text-slate-400 font-medium mb-1">Prolanis HT</p>
                                <p id="modal-prolanis-ht" class="text-sm font-bold text-slate-700">-</p>
                            </div>
                            <div class="bg-blue-50 rounded-xl p-3">
                                <p class="text-xs text-slate-400 font-medium mb-1">Peserta PRB</p>
                                <p id="modal-peserta-prb" class="text-sm font-bold text-slate-700">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tempat Tidur (FKRTL only) --}}
                <div id="modal-tt-wrap" class="hidden">
                    <div class="border-t border-slate-100 pt-4">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Jumlah Tempat Tidur Rawat Inap
                        </p>
                        <div id="modal-tt-grid" class="grid grid-cols-2 gap-2"></div>
                    </div>
                </div>

                {{-- Layanan --}}
                <div id="modal-layanan-wrap" class="hidden">
                    <div class="border-t border-slate-100 pt-4">
                        <p
                            class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Layanan Tersedia
                        </p>
                        <div id="modal-layanan" class="flex flex-wrap gap-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // ── Init Map ──
        const map = L.map('map', {
            zoomControl: false
        }).setView([-0.3, 100.1], 9);

        // ── Custom Icons ──
        const iconFKRTL = L.divIcon({
            className: '',
            html: `
        <div style="
            background: #dc2626;
            border: 2.5px solid white;
            border-radius: 50%;
            width: 28px; height: 28px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 2px 8px rgba(220,38,38,0.5);
        ">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </div>`,
            iconSize: [28, 28],
            iconAnchor: [14, 14],
            popupAnchor: [0, -16],
        });

        const iconFKTP = L.divIcon({
            className: '',
            html: `
        <div style="
            background: #16a34a;
            border: 2.5px solid white;
            border-radius: 50%;
            width: 24px; height: 24px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 2px 8px rgba(22,163,74,0.5);
        ">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </div>`,
            iconSize: [24, 24],
            iconAnchor: [12, 12],
            popupAnchor: [0, -14],
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: ''
        }).addTo(map);

        L.control.zoom({
            position: 'topright'
        }).addTo(map);

        // ── State ──
        let kabkotaLayer = null;
        let kecamatanLayer = null;
        let markerLayer = L.layerGroup().addTo(map);
        let activeKabkotaId = null;
        let kabkotaData = [];

        // ── Styles ──
        const styleDefault = {
            color: '#0052A5',
            weight: 2,
            fillColor: '#3b82f6',
            fillOpacity: 0.15,
        };
        const styleHover = {
            fillOpacity: 0.35,
            weight: 3
        };
        const styleKecamatan = {
            color: '#7c3aed',
            weight: 1.5,
            fillColor: '#8b5cf6',
            fillOpacity: 0.15,
        };
        const styleKecamatanHover = {
            fillOpacity: 0.4,
            weight: 2.5
        };

        // ── Load GeoJSON Kab/Kota ──
        fetch('/geojson/wilayah.geojson')
            .then(r => r.json())
            .then(geojson => {
                document.getElementById('loading').style.display = 'none';

                kabkotaLayer = L.geoJSON(geojson, {
                    style: styleDefault,
                    onEachFeature: (feature, layer) => {
                        const label = feature.properties.label;
                        layer.bindTooltip(label, {
                            sticky: true
                        });
                        layer.on({
                            mouseover: e => e.target.setStyle(styleHover),
                            mouseout: e => kabkotaLayer.resetStyle(e.target),
                            click: e => onKabkotaClick(feature, layer),
                        });
                    }
                }).addTo(map);

                map.fitBounds(kabkotaLayer.getBounds());
            });

        // ── Load Data Kabkota (sidebar) ──
        fetch('/api/kabkota')
            .then(r => r.json())
            .then(data => {
                kabkotaData = data;
                const container = document.getElementById('kabkota-list');
                container.innerHTML = '';

                data.forEach(item => {
                    const card = document.createElement('div');
                    card.className =
                        'kabkota-card group flex items-center justify-between bg-white border border-slate-100 hover:border-primary/30 hover:bg-primary/5 rounded-xl px-4 py-3 cursor-pointer transition-all duration-200 shadow-sm hover:shadow-md';
                    card.dataset.id = item.id;
                    card.innerHTML = `
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-primary/10 group-hover:bg-primary/20 rounded-lg flex items-center justify-center transition-colors flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-sm font-semibold text-slate-700 group-hover:text-primary transition-colors">${item.nama}</span>
                            <p class="text-xs text-slate-400 mt-0.5">${item.populasi ? item.populasi.toLocaleString('id-ID') + ' jiwa' : ''}</p>
                        </div>
                    </div>
                    <div class="flex flex-col items-end gap-1">
                        <span class="text-xs bg-emerald-50 text-emerald-700 font-semibold px-2 py-0.5 rounded-full">FKTP: ${item.fktp_count}</span>
                        <span class="text-xs bg-red-50 text-red-700 font-semibold px-2 py-0.5 rounded-full">FKRTL: ${item.fkrtl_count}</span>
                    </div>
                `;
                    card.addEventListener('click', () => zoomToKabkota(item.nama));
                    container.appendChild(card);
                });
            });

        // ── Klik Kab/Kota di Peta ──
        function onKabkotaClick(feature, layer) {
            const label = feature.properties.label;
            const slug = feature.properties.slug;

            console.log('Klik:', label, '| slug:', slug);

            const kabkota = kabkotaData.find(k =>
                k.nama.toLowerCase().replace('kabupaten ', '').replace('kota ', '') ===
                feature.properties.NAME_2.replace(/([a-z])([A-Z])/g, '$1 $2').toLowerCase()
            );

            map.fitBounds(layer.getBounds(), {
                padding: [40, 40]
            });

            if (kecamatanLayer) map.removeLayer(kecamatanLayer);

            fetch(`/geojson/kecamatan/${slug}.geojson`)
                .then(r => {
                    if (!r.ok) throw new Error(`HTTP ${r.status}`);
                    return r.json();
                })
                .then(geojson => {
                    kecamatanLayer = L.geoJSON(geojson, {
                        style: styleKecamatan,
                        onEachFeature: (feat, lyr) => {
                            lyr.bindTooltip(feat.properties.label, {
                                sticky: true
                            });
                            lyr.on({
                                mouseover: e => e.target.setStyle(styleKecamatanHover),
                                mouseout: e => kecamatanLayer.resetStyle(e.target),
                                click: e => onKecamatanClick(feat, lyr),
                            });
                        }
                    }).addTo(map);
                })
                .catch(err => console.error('Error load kecamatan:', err));

            showDetailPanel(label, kabkota?.id);
        }

        // ── Klik Kecamatan ──
        function onKecamatanClick(feature, layer) {
            map.fitBounds(layer.getBounds(), {
                padding: [20, 20]
            });
            showKecamatanPanel(feature.properties.label, activeKabkotaId);
        }

        // ── Show Detail Panel ──
        function showDetailPanel(namaKabkota, kabkotaId) {
            activeKabkotaId = kabkotaId;
            document.getElementById('list-panel').classList.add('hidden');
            document.getElementById('detail-panel').classList.remove('hidden');
            document.getElementById('kecamatan-panel').classList.add('hidden');
            document.getElementById('detail-title').textContent = namaKabkota;

            // Ambil populasi dari kabkotaData
            const kabkota = kabkotaData.find(k => k.id === kabkotaId);
            const popEl = document.getElementById('detail-populasi');
            popEl.textContent = kabkota?.populasi 
                ? Number(kabkota.populasi).toLocaleString('id-ID') + ' jiwa' 
                : '';

            if (!kabkotaId) return;

            const container = document.getElementById('fkrtl-list');
            container.innerHTML =
                `<div class="flex items-center gap-2 text-xs text-slate-400 py-2"><div class="spinner"></div> Memuat data...</div>`;

            fetch(`/api/kabkota/${kabkotaId}/faskes`)
                .then(r => r.json())
                .then(data => {
                    if (data.length === 0) {
                        container.innerHTML = emptyState('Belum ada data FKRTL di wilayah ini');
                        return;
                    }
                    container.innerHTML = sectionHeader('FKRTL', data.length, 'red');
                    data.forEach((f, i) => {
                        container.innerHTML += faskesCard(f, 'fkrtl', i);
                    });
                });
            
            loadMarkers(kabkotaId); // ← tambah ini
        }

        // ── Show Kecamatan Panel ──
        function showKecamatanPanel(namaKecamatan, kabkotaId) {
            document.getElementById('list-panel').classList.add('hidden');
            document.getElementById('detail-panel').classList.add('hidden');
            document.getElementById('kecamatan-panel').classList.remove('hidden');
            // document.getElementById('kecamatan-title').textContent = namaKecamatan;
            document.getElementById('kecamatan-title').innerHTML = namaKecamatan;

            const container = document.getElementById('kecamatan-faskes');
            container.innerHTML =
                `<div class="flex items-center gap-2 text-xs text-slate-400 py-2"><div class="spinner"></div> Memuat data...</div>`;

            fetch(`/api/kecamatan/cari?nama=${encodeURIComponent(namaKecamatan)}&kabkota_id=${kabkotaId}`)
                .then(r => r.json())
                .then(data => {
                    let html = '';
                    const fktp = data.fktp ?? [];
                    const fkrtl = data.fkrtl ?? [];

                    // Tampilkan populasi kecamatan
                    const popEl = document.getElementById('kecamatan-populasi');
                    if (data.populasi) {
                        popEl.textContent = data.populasi.toLocaleString('id-ID') + ' jiwa';
                    } else {
                        popEl.textContent = '';
                    }

                    if (fktp.length === 0 && fkrtl.length === 0) {
                        html = emptyState('Belum ada data faskes di kecamatan ini');
                    }
                    if (fktp.length > 0) {
                        html += sectionHeader('FKTP', fktp.length, 'green');
                        fktp.forEach((f, i) => {
                            html += faskesCard(f, 'fktp', i);
                        });
                    }
                    if (fkrtl.length > 0) {
                        html += sectionHeader('FKRTL', fkrtl.length, 'red');
                        fkrtl.forEach((f, i) => {
                            html += faskesCard(f, 'fkrtl', i);
                        });
                    }
                    container.innerHTML = html;
                })
                .catch(() => {
                    container.innerHTML = emptyState('Kecamatan belum ada di database');
                });
        }

        // ── Helpers ──
        function sectionHeader(label, count, color) {
            const colors = {
                green: 'bg-emerald-50 text-emerald-700 border-emerald-100',
                red: 'bg-red-50 text-red-700 border-red-100',
            };
            return `
            <div class="flex items-center justify-between mb-2 mt-1">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">${label}</span>
                <span class="text-xs font-bold px-2 py-0.5 rounded-full border ${colors[color]}">${count} faskes</span>
            </div>
        `;
        }

        function faskesCard(f, tipe, index) {
            const borderColor = tipe === 'fktp' ? 'border-l-emerald-400' : 'border-l-red-400';
            const delay = `style="animation-delay:${index * 0.05}s"`;
            return `
            <div class="faskes-item group bg-white border border-slate-100 border-l-4 ${borderColor}
                        rounded-r-xl px-3 py-2.5 cursor-pointer hover:shadow-md hover:border-slate-200
                        transition-all duration-150 mb-2"
                 onclick='openModal(${JSON.stringify(f)})' ${delay}>
                <p class="text-sm font-semibold text-slate-700 group-hover:text-primary transition-colors leading-snug">${f.nama}</p>
                <p class="text-xs text-slate-400 mt-0.5">${f.tipe_detail ?? ''} ${f.kelas ? '· ' + f.kelas : ''}</p>
            </div>
        `;
        }

        function emptyState(msg) {
            return `
            <div class="flex flex-col items-center py-8 text-center">
                <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-xs text-slate-400 font-medium">${msg}</p>
            </div>
        `;
        }

        // ── Navigation ──
        function backToList() {
            document.getElementById('list-panel').classList.remove('hidden');
            document.getElementById('detail-panel').classList.add('hidden');
            document.getElementById('kecamatan-panel').classList.add('hidden');
            if (kecamatanLayer) map.removeLayer(kecamatanLayer);
            map.fitBounds(kabkotaLayer.getBounds());

            markerLayer.clearLayers(); // ← tambah ini
        }

        function backToKabkota() {
            document.getElementById('list-panel').classList.add('hidden');
            document.getElementById('detail-panel').classList.remove('hidden');
            document.getElementById('kecamatan-panel').classList.add('hidden');
        }

        function zoomToKabkota(nama) {
            if (!kabkotaLayer) return;
            kabkotaLayer.eachLayer(layer => {
                const label = layer.feature.properties.label;
                if (label.toLowerCase().includes(nama.toLowerCase().replace('kabupaten ', '').replace('kota ',
                        ''))) {
                    map.fitBounds(layer.getBounds(), {
                        padding: [40, 40]
                    });
                    onKabkotaClick(layer.feature, layer);
                }
            });
        }

        // ── Modal ──
        function openModal(faskes) {
            const isFKTP = faskes.tipe === 'FKTP';
            const kelasContainer = document.getElementById('modal-kelas-container');
            const tipeFktpContainer = document.getElementById('modal-tipe-fktp-container');

            // FKTP → tampilkan Tipe FKTP, sembunyikan Kelas
            if (isFKTP) {
                kelasContainer.classList.add('hidden');
                tipeFktpContainer.classList.remove('hidden');

                document.getElementById('modal-tipe-fktp-badge').textContent =
                    faskes.tipe_fktp || '-';
            } else {
                kelasContainer.classList.remove('hidden');
                tipeFktpContainer.classList.add('hidden');

                document.getElementById('modal-kelas').textContent =
                    faskes.kelas || '-';
            }
            // Badge tipe di header
            document.getElementById('modal-tipe-badge').innerHTML = `
            <span class="inline-flex items-center gap-1 text-xs font-bold px-2.5 py-1 rounded-full
                         ${isFKTP ? 'bg-emerald-400/20 text-emerald-100' : 'bg-red-400/20 text-red-100'}">
                <span class="w-1.5 h-1.5 rounded-full pulse-dot ${isFKTP ? 'bg-emerald-300' : 'bg-red-300'}"></span>
                ${faskes.tipe}
            </span>
        `;

            document.getElementById('modal-nama').textContent = faskes.nama;
            document.getElementById('modal-subtitle').querySelector('span').textContent = [faskes.kecamatan?.nama, faskes
                .kabupaten_kota?.nama
            ].filter(Boolean).join(' · ') || '-';

            document.getElementById('modal-tipe-detail').textContent = faskes.tipe_detail || '-';
            // document.getElementById('modal-kelas').textContent = faskes.kelas || '-';    

            // Status badge
            document.getElementById('modal-status-badge').innerHTML = faskes.status_aktif ?
                `<span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-100 px-3 py-1.5 rounded-lg">
                   <span class="w-2 h-2 bg-emerald-500 rounded-full"></span> Aktif & Beroperasi
               </span>` :
                `<span class="inline-flex items-center gap-1.5 text-xs font-semibold text-red-700 bg-red-50 border border-red-100 px-3 py-1.5 rounded-lg">
                   <span class="w-2 h-2 bg-red-500 rounded-full"></span> Tidak Aktif
               </span>`;

            // Tipe FKTP badge
            document.getElementById('modal-tipe-fktp-badge').textContent =
            faskes.tipe === 'FKTP' && faskes.tipe_fktp
                ? faskes.tipe_fktp
                : '';

            // Contact fields
            const setField = (id, rowId, val) => {
                document.getElementById(id).textContent = val || '-';
                document.getElementById(rowId).style.display = val ? 'flex' : 'none';
            };
            setField('modal-alamat', 'modal-alamat-row', faskes.alamat);
            setField('modal-telepon', 'modal-telepon-row', faskes.telepon);
            setField('modal-email', 'modal-email-row', faskes.email);
            setField('modal-website', 'modal-website-row', faskes.website);

            // Layanan
            const layananWrap = document.getElementById('modal-layanan-wrap');
            if (faskes.layanans && faskes.layanans.length > 0) {
                layananWrap.classList.remove('hidden');
                document.getElementById('modal-layanan').innerHTML = faskes.layanans
                    .map(l => {
                        const status = l.pivot?.status_layanan;
                        return `<span class="text-xs bg-blue-50 text-blue-700 border border-blue-100 font-semibold px-2.5 py-1 rounded-full">
                            ${l.nama_layanan}${status ? ' (' + status + ')' : ''}
                        </span>`;
                    })
                    .join('');
            } else {
                layananWrap.classList.add('hidden');
            }

            const overlay = document.getElementById('modalOverlay');
            overlay.classList.remove('hidden');
            overlay.classList.add('flex');

            // Peserta terdaftar (FKTP)
            const pesertaWrap = document.getElementById('modal-peserta-wrap');
            if (!isFKTP) {
                pesertaWrap.classList.add('hidden');
            } else {
                const hasPeserta = faskes.peserta_terdaftar || faskes.prolanis_dm || faskes.prolanis_ht || faskes.peserta_prb;
                if (hasPeserta) {
                    pesertaWrap.classList.remove('hidden');
                    document.getElementById('modal-peserta-terdaftar').textContent = faskes.peserta_terdaftar?.toLocaleString('id-ID') ?? '-';
                    document.getElementById('modal-prolanis-dm').textContent = faskes.prolanis_dm?.toLocaleString('id-ID') ?? '-';
                    document.getElementById('modal-prolanis-ht').textContent = faskes.prolanis_ht?.toLocaleString('id-ID') ?? '-';
                    document.getElementById('modal-peserta-prb').textContent = faskes.peserta_prb?.toLocaleString('id-ID') ?? '-';
                } else {
                    pesertaWrap.classList.add('hidden');
                }
            }

            // Tempat tidur (FKRTL)
            const ttWrap = document.getElementById('modal-tt-wrap');
            const ttGrid = document.getElementById('modal-tt-grid');
            if (isFKTP || !faskes.fasilitas_tempat_tidurs || faskes.fasilitas_tempat_tidurs.length === 0) {
                ttWrap.classList.add('hidden');
            } else {
                ttWrap.classList.remove('hidden');
                ttGrid.innerHTML = faskes.fasilitas_tempat_tidurs.map(tt => `
                    <div class="bg-red-50 rounded-xl p-3">
                        <p class="text-xs text-slate-400 font-medium mb-1">${tt.kelas_tempat_tidur?.nama ?? 'Kelas ' + tt.kelas_tempat_tidur_id}</p>
                        <p class="text-sm font-bold text-slate-700">${tt.jumlah ?? 0}</p>
                    </div>
                `).join('');
            }
        }

        function closeModal(event) {
            if (event.target === document.getElementById('modalOverlay')) closeModalDirect();
        }

        function closeModalDirect() {
            const overlay = document.getElementById('modalOverlay');
            overlay.classList.add('hidden');
            overlay.classList.remove('flex');
        }
    </script>

    <script>
        // ── Swipe / Drag Bottom Sheet ──
        const sidebar = document.getElementById('sidebar');
        const handle = document.querySelector('.drag-handle');
        let startY = 0;
        let startHeight = 0;
        let isDragging = false;

        const SNAP_COLLAPSED = 80; // px — tinggi minimal (cuma header keliatan)
        const SNAP_MID = 55; // % viewport height — posisi default
        const SNAP_FULL = 90; // % viewport height — full

        function isMobile() {
            return window.innerWidth < 768;
        }

        // function setHeight(px, animate = true) {
        //     if (!isMobile()) return;
        //     sidebar.style.transition = animate ? 'height 0.3s cubic-bezier(0.4,0,0.2,1)' : 'none';
        //     sidebar.style.height = px + 'px';
        //     sidebar.style.maxHeight = px + 'px';
        // }

        function setHeight(px, animate = true) {
            if (!isMobile()) return;
            if (animate) {
                sidebar.style.transition = 'height 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
            } else {
                sidebar.style.transition = 'none';
            }
            sidebar.style.height = px + 'px';
            sidebar.style.maxHeight = px + 'px';
        }

        function snapTo(state) {
            const vh = window.innerHeight;
            if (state === 'collapsed') setHeight(SNAP_COLLAPSED);
            if (state === 'mid') setHeight(vh * SNAP_MID / 100);
            if (state === 'full') setHeight(vh * SNAP_FULL / 100);
        }

        // Touch events
        // handle.addEventListener('touchstart', e => {
        //     if (!isMobile()) return;
        //     isDragging = true;
        //     startY = e.touches[0].clientY;
        //     startHeight = sidebar.offsetHeight;
        //     sidebar.style.transition = 'none';
        // }, {
        //     passive: true
        // });

        handle.addEventListener('touchstart', e => {
            if (!isMobile()) return;
            isDragging = true;
            startY = e.touches[0].clientY;
            startHeight = sidebar.offsetHeight;
            // Matikan transisi setelah frame berikutnya biar ga glitch
            requestAnimationFrame(() => {
                sidebar.style.transition = 'none';
            });
        }, {
            passive: true
        });

        handle.addEventListener('touchmove', e => {
            if (!isDragging || !isMobile()) return;
            const dy = startY - e.touches[0].clientY;
            const newHeight = Math.min(
                Math.max(startHeight + dy, SNAP_COLLAPSED),
                window.innerHeight * SNAP_FULL / 100
            );
            sidebar.style.height = newHeight + 'px';
            sidebar.style.maxHeight = newHeight + 'px';
        }, {
            passive: true
        });

        handle.addEventListener('touchend', e => {
            if (!isDragging || !isMobile()) return;
            isDragging = false;

            const currentH = sidebar.offsetHeight;
            const vh = window.innerHeight;
            const midH = vh * SNAP_MID / 100;
            const fullH = vh * SNAP_FULL / 100;

            // Hitung velocity (kecepatan swipe)
            const endY = e.changedTouches[0].clientY;
            const velocity = endY - startY; // positif = swipe ke bawah

            // Kalau swipe ke bawah cepat → langsung collapsed
            if (velocity > 60) {
                snapTo('collapsed');
                return;
            }

            // Kalau swipe ke atas cepat → langsung full
            if (velocity < -60) {
                snapTo(currentH > midH * 0.8 ? 'full' : 'mid');
                return;
            }

            // Snap ke posisi terdekat
            const distCollapsed = Math.abs(currentH - SNAP_COLLAPSED);
            const distMid = Math.abs(currentH - midH);
            const distFull = Math.abs(currentH - fullH);

            if (distCollapsed <= distMid && distCollapsed <= distFull) snapTo('collapsed');
            else if (distMid <= distFull) snapTo('mid');
            else snapTo('full');
        });

        // Mouse events (buat testing di desktop mode)
        handle.addEventListener('mousedown', e => {
            if (!isMobile()) return;
            isDragging = true;
            startY = e.clientY;
            startHeight = sidebar.offsetHeight;
            sidebar.style.transition = 'none';
        });

        document.addEventListener('mousemove', e => {
            if (!isDragging || !isMobile()) return;
            const dy = startY - e.clientY;
            const newHeight = Math.min(
                Math.max(startHeight + dy, SNAP_COLLAPSED),
                window.innerHeight * SNAP_FULL / 100
            );
            sidebar.style.height = newHeight + 'px';
            sidebar.style.maxHeight = newHeight + 'px';
        });

        document.addEventListener('mouseup', e => {
            if (!isDragging || !isMobile()) return;
            isDragging = false;

            const currentH = sidebar.offsetHeight;
            const vh = window.innerHeight;
            const midH = vh * SNAP_MID / 100;
            const fullH = vh * SNAP_FULL / 100;
            const velocity = e.clientY - startY;

            if (velocity > 60) snapTo('collapsed');
            else if (velocity < -60) snapTo(currentH > midH * 0.8 ? 'full' : 'mid');
            else {
                const distCollapsed = Math.abs(currentH - SNAP_COLLAPSED);
                const distMid = Math.abs(currentH - midH);
                const distFull = Math.abs(currentH - fullH);

                if (distCollapsed <= distMid && distCollapsed <= distFull) snapTo('collapsed');
                else if (distMid <= distFull) snapTo('mid');
                else snapTo('full');
            }
        });

        initMobileHeight();
        window.addEventListener('resize', initMobileHeight);
    </script>

    <script>
        function loadMarkers(kabkotaId) {
            markerLayer.clearLayers();

            fetch(`/api/kabkota/${kabkotaId}/markers`)
                .then(r => r.json())
                .then(data => {
                    data.forEach(f => {
                        if (!f.lat || !f.lng) return;

                        const icon = f.tipe === 'FKRTL' ? iconFKRTL : iconFKTP;
                        const marker = L.marker([f.lat, f.lng], {
                            icon
                        });

                        marker.bindPopup(`
                    <div style="font-family:'Plus Jakarta Sans',sans-serif; min-width:180px">
                        <div style="font-weight:700; font-size:13px; color:#1e293b; margin-bottom:4px">${f.nama}</div>
                        <div style="font-size:11px; color:#64748b">${f.tipe_detail ?? ''} ${f.kelas ? '· ' + f.kelas : ''}</div>
                        ${f.alamat ? `<div style="font-size:11px; color:#94a3b8; margin-top:4px">📍 ${f.alamat}</div>` : ''}
                        <div style="margin-top:8px">
                            <span style="
                                font-size:10px; font-weight:700; padding:2px 8px; border-radius:99px;
                                background:${f.tipe === 'FKRTL' ? '#fee2e2' : '#dcfce7'};
                                color:${f.tipe === 'FKRTL' ? '#991b1b' : '#166534'};
                            ">${f.tipe}</span>
                        </div>
                    </div>
                `, {
                            maxWidth: 250
                        });

                        markerLayer.addLayer(marker);
                    });
                });
        }
    </script>

    <script>
        // ── Search Faskes ──
        const searchInput  = document.getElementById('search-input');
        const searchClear  = document.getElementById('search-clear');
        const sidebarContent = document.getElementById('sidebar-content');

        let searchTimeout = null;
        let searchResultPanel = null;

        function getOrCreateSearchPanel() {
            if (!searchResultPanel) {
                searchResultPanel = document.createElement('div');
                searchResultPanel.id = 'search-result-panel';
                searchResultPanel.className = 'p-4';
                sidebarContent.appendChild(searchResultPanel);
            }
            return searchResultPanel;
        }

        function hideAllPanels() {
            document.getElementById('list-panel').classList.add('hidden');
            document.getElementById('detail-panel').classList.add('hidden');
            document.getElementById('kecamatan-panel').classList.add('hidden');
        }

        function restoreActivePanels() {
            // Kembalikan ke panel yang aktif sebelum search
            const hasActive = activeKabkotaId !== null;
            document.getElementById('list-panel').classList.toggle('hidden', hasActive);
            document.getElementById('detail-panel').classList.toggle('hidden', !hasActive);
            document.getElementById('kecamatan-panel').classList.add('hidden');
        }

        function clearSearch() {
            searchInput.value = '';
            searchClear.classList.add('hidden');
            if (searchResultPanel) {
                searchResultPanel.remove();
                searchResultPanel = null;
            }
            restoreActivePanels();
        }

        searchClear.addEventListener('click', clearSearch);

        searchInput.addEventListener('input', () => {
            const q = searchInput.value.trim();

            searchClear.classList.toggle('hidden', q.length === 0);

            if (q.length === 0) {
                clearSearch();
                return;
            }

            if (q.length < 2) return;

            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => doSearch(q), 350);
        });

        function doSearch(q) {
            hideAllPanels();

            const panel = getOrCreateSearchPanel();
            panel.classList.remove('hidden');
            panel.innerHTML = `<div class="flex items-center gap-2 text-xs text-slate-400 py-2">
                <div class="spinner"></div> Mencari "<strong>${q}</strong>"...
            </div>`;

            fetch(`/api/faskes/cari?q=${encodeURIComponent(q)}`)
                .then(r => r.json())
                .then(data => {
                    const fktp  = data.fktp  ?? [];
                    const fkrtl = data.fkrtl ?? [];
                    let html = '';

                    if (fktp.length === 0 && fkrtl.length === 0) {
                        html = emptyState(`Tidak ada faskes dengan nama "<strong>${q}</strong>"`);
                    } else {
                        html += `<div class="flex items-center gap-2 mb-3">
                            <div class="w-1 h-4 bg-primary rounded-full"></div>
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                                Hasil Pencarian
                                <span class="normal-case font-semibold text-slate-400 ml-1">"${q}"</span>
                            </p>
                        </div>`;

                        if (fktp.length > 0) {
                            html += sectionHeader('FKTP', fktp.length, 'green');
                            fktp.forEach((f, i) => { html += faskesCard(f, 'fktp', i); });
                        }
                        if (fkrtl.length > 0) {
                            html += sectionHeader('FKRTL', fkrtl.length, 'red');
                            fkrtl.forEach((f, i) => { html += faskesCard(f, 'fkrtl', i); });
                        }
                    }

                    panel.innerHTML = html;
                })
                .catch(() => {
                    panel.innerHTML = emptyState('Gagal melakukan pencarian');
                });
        }
    </script>
</body>

</html>
