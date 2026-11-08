@if(session('success'))
    <div class="rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 mb-4">
        {{ session('success') }}
    </div>
@endif

@if(session('import_errors') && count(session('import_errors')))
    <div class="rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 mb-4">
        <p class="font-semibold mb-1">{{ count(session('import_errors')) }} baris dilewati:</p>
        <ul class="list-disc pl-5 space-y-0.5 max-h-48 overflow-y-auto break-words">
            @foreach(session('import_errors') as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

@error('file')
    <div class="rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 mb-4">{{ $message }}</div>
@enderror