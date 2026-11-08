<dialog id="{{ $modalId }}"
    class="w-[calc(100%-2rem)] max-w-md max-h-[90vh] overflow-y-auto rounded-2xl p-0 m-auto shadow-xl backdrop:bg-slate-900/40">
    <form method="POST" action="{{ $action }}" enctype="multipart/form-data">
        @csrf

        <div class="px-4 sm:px-5 py-4 border-b border-slate-100 flex items-start justify-between gap-3">
            <div class="min-w-0">
                <h3 class="text-base font-semibold text-slate-800">{{ $title }}</h3>
                <p class="text-xs text-slate-400 mt-0.5">{{ $hint }}</p>
            </div>
            <button type="button" onclick="this.closest('dialog').close()" aria-label="Tutup"
                class="text-slate-400 hover:text-slate-600 text-2xl leading-none shrink-0">&times;</button>
        </div>

        <div class="p-4 sm:p-5 space-y-4">
            <a href="{{ $template }}" class="inline-flex items-center gap-2 text-sm text-blue-700 hover:underline">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Unduh template Excel
            </a>

            <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="w-full text-sm text-slate-600 border border-slate-300 rounded-lg
                       file:mr-3 file:py-2.5 file:px-4 file:border-0 file:bg-blue-50 file:text-blue-700
                       file:font-medium hover:file:bg-blue-100">

            <p class="text-xs text-slate-400">
                Format .xlsx, .xls, atau .csv, maksimal 5 MB. Baris yang salah akan dilewati dan ditampilkan.
            </p>
        </div>

        <div
            class="px-4 sm:px-5 py-4 border-t border-slate-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-2 sm:gap-3">
            <button type="button" onclick="this.closest('dialog').close()"
                class="w-full sm:w-auto px-5 py-2.5 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-50 text-sm transition">
                Batal
            </button>
            <button type="submit" data-submit
                class="w-full sm:w-auto bg-blue-700 hover:bg-blue-800 disabled:opacity-60 text-white font-semibold px-6 py-2.5 rounded-lg text-sm transition">
                Import
            </button>
        </div>
    </form>
</dialog>

<script>
    (function () {
        const dialog = document.getElementById('{{ $modalId }}');
        if (!dialog) return;

        // klik area gelap di luar kotak = tutup
        dialog.addEventListener('click', function (e) {
            if (e.target === dialog) dialog.close();
        });

        // cegah klik ganda: proses hash password butuh beberapa detik
        const form = dialog.querySelector('form');
        const btn = dialog.querySelector('[data-submit]');
        form.addEventListener('submit', function () {
            btn.disabled = true;
            btn.textContent = 'Mengimport...';
        });
    })();
</script>