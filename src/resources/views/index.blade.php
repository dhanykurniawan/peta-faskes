<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Integrasi</title>

    {{-- @vite(['resources/css/app.css']) --}}
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50">

    <div class="flex min-h-screen items-center justify-center px-6">
        <div class="w-full max-w-4xl">

            <!-- Header -->
            <div class="text-center mb-10">
                <h1 class="text-4xl font-bold text-slate-800">
                    Dashboard Integrasi
                </h1>
                <p class="mt-3 text-slate-500 text-lg">
                    Pilih aplikasi yang ingin diakses
                </p>
            </div>

            <!-- Menu -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Lookers Studio -->
                <a href="https://datastudio.google.com/reporting/2408a377-92dc-44af-b0ef-887c45c21065"
                   class="group bg-white rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 p-10 border border-slate-200 hover:-translate-y-2" target="_blank">

                    <div class="flex flex-col items-center text-center">
                        <div class="w-20 h-20 rounded-2xl bg-blue-100 flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="w-10 h-10 text-blue-600"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M9 17v-6m4 6V7m4 10v-3M5 19h14"/>
                            </svg>
                        </div>

                        <h2 class="text-2xl font-semibold text-slate-800 group-hover:text-blue-600">
                            SDM Faskes
                        </h2>

                        <p class="mt-3 text-slate-500">
                            Dashboard sebaran SDM pada masing-masing fasilitas kesehatan.
                        </p>
                    </div>
                </a>

                <!-- Peta Faskes -->
                <a href="peta"
                   class="group bg-white rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 p-10 border border-slate-200 hover:-translate-y-2" target="_blank">

                    <div class="flex flex-col items-center text-center">
                        <div class="w-20 h-20 rounded-2xl bg-green-100 flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="w-10 h-10 text-green-600"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>

                        <h2 class="text-2xl font-semibold text-slate-800 group-hover:text-green-600">
                            Peta Faskes
                        </h2>

                        <p class="mt-3 text-slate-500">
                            Informasi dan persebaran fasilitas kesehatan.
                        </p>
                    </div>
                </a>

            </div>

            <!-- Footer -->
            <div class="text-center mt-10 text-sm text-slate-400">
                © {{ date('Y') }} BPJS Kesehatan KC Bukittinggi.
                <br>
                All Rights Reserved.
            </div>

        </div>
    </div>

</body>
</html>