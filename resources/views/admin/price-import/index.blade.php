<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('Import Reference Prices (DA Bulletin)') }}
        </h2>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 animate-fade-in-up">

        {{-- ================================================================
             LEFT PANEL — Upload Form & Instructions
             ================================================================ --}}
        <div class="md:col-span-1 space-y-6">

            {{-- Upload Form --}}
            <div class="card bg-base-100 shadow-sm border border-base-300 sticky top-24">
                <div class="card-body">
                    <h2 class="card-title text-lg mb-1">Upload Price CSV</h2>
                    <p class="text-sm text-base-content/60 mb-4">
                        Upload a DA Bantay Presyo price bulletin exported as a CSV file.
                        Prices will be imported as reference data for the forecasting feature.
                    </p>

                    @if(session('import_success'))
                        <div class="alert alert-success mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ session('import_success') }}</span>
                        </div>
                    @endif

                    @if($errors->has('csv_file'))
                        <div class="alert alert-error mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ $errors->first('csv_file') }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.price-import.store') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text font-medium">CSV File</span>
                                <span class="label-text-alt text-base-content/50">Max 2MB</span>
                            </label>
                            <input
                                type="file"
                                name="csv_file"
                                accept=".csv,.txt"
                                class="file-input file-input-bordered w-full @error('csv_file') file-input-error @enderror"
                                required
                            />
                        </div>

                        <button type="submit" class="btn btn-primary w-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            Import Prices
                        </button>
                    </form>
                </div>
            </div>

            {{-- Source Link Form --}}
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <h2 class="card-title text-lg mb-1">DA Price Source Links</h2>
                    <p class="text-sm text-base-content/60 mb-4">
                        Provide a direct link to the DA Bantay Presyo page per crop so farmers can verify the prices.
                    </p>

                    @if(session('link_success'))
                        <div class="alert alert-success mb-4 p-2 text-sm flex gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span>{{ session('link_success') }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.price-import.source-link') }}" class="space-y-3">
                        @csrf
                        @foreach($crops as $crop)
                            <div class="form-control w-full">
                                <label class="label pb-1">
                                    <span class="label-text font-medium">{{ $crop->name }}</span>
                                </label>
                                <input
                                    type="url"
                                    name="source_links[{{ $crop->id }}]"
                                    value="{{ old('source_links.' . $crop->id, $sourceLinks['da_price_source_link_' . $crop->id] ?? '') }}"
                                    placeholder="https://..."
                                    class="input input-bordered input-sm w-full @error('source_links.' . $crop->id) input-error @enderror"
                                />
                                @error('source_links.' . $crop->id)
                                    <span class="label-text-alt text-error mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        @endforeach

                        <button type="submit" class="btn btn-secondary btn-sm w-full mt-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                            Save Links
                        </button>
                    </form>
                </div>
            </div>

            {{-- CSV Format Guide --}}
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <h3 class="font-bold text-base mb-2">📄 Required CSV Format</h3>
                    <p class="text-xs text-base-content/60 mb-3">
                        The first row must be the header. Columns must be in this exact order:
                    </p>
                    <div class="overflow-x-auto">
                        <table class="table table-xs w-full border border-base-300 rounded-lg">
                            <thead class="bg-base-200">
                                <tr>
                                    <th>Column</th>
                                    <th>Example</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td class="font-mono text-xs">crop</td><td class="text-xs">Palay</td></tr>
                                <tr><td class="font-mono text-xs">specification</td><td class="text-xs">dry</td></tr>
                                <tr><td class="font-mono text-xs">price_per_kg</td><td class="text-xs">20.50</td></tr>
                                <tr><td class="font-mono text-xs">recorded_at</td><td class="text-xs">2026-08-05</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="divider my-3"></div>

                    <p class="text-xs font-semibold text-base-content/70 mb-1">Valid Crops:</p>
                    <div class="flex flex-wrap gap-1">
                        <span class="badge badge-ghost badge-sm">Palay</span>
                        <span class="badge badge-ghost badge-sm">Mais</span>
                        <span class="badge badge-ghost badge-sm">Munggo</span>
                    </div>

                    <div class="divider my-3"></div>

                    <a href="data:text/csv;charset=utf-8,crop%2Cspecification%2Cprice_per_kg%2Crecorded_at%0APalay%2Cdry%2C20.50%2C2026-08-05%0APalay%2Cbasa%2C18.00%2C2026-08-05%0AMais%2Cyellow%20(dry)%2C15.00%2C2026-08-05%0AMais%2Cyellow%20(basa)%2C13.50%2C2026-08-05%0AMais%2Cwhite%2C14.00%2C2026-08-05%0AMunggo%2Ckusapo%2C75.00%2C2026-08-05%0AMunggo%2Cbullad%2C80.00%2C2026-08-05"
                       download="sample_price_import.csv"
                       class="btn btn-outline btn-sm w-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download Sample CSV
                    </a>
                </div>
            </div>
        </div>

        {{-- ================================================================
             RIGHT PANEL — Import Results
             ================================================================ --}}
        <div class="md:col-span-2">
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body p-0">
                    <div class="p-6 border-b border-base-200">
                        <h2 class="card-title text-lg">Import Results</h2>
                        <p class="text-sm text-base-content/60 mt-1">
                            After uploading a CSV, a summary of imported and skipped rows will appear here.
                        </p>
                    </div>

                    <div class="p-6">
                        @if(session('import_success'))
                            {{-- Success Summary --}}
                            <div class="mb-6">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="badge badge-success badge-lg gap-2 p-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Import Complete
                                    </div>
                                </div>
                                <p class="text-base font-medium text-base-content">{{ session('import_success') }}</p>
                            </div>

                            @if(session('import_errors') && count(session('import_errors')) > 0)
                                <div>
                                    <h3 class="font-semibold text-warning mb-2 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        {{ count(session('import_errors')) }} Row(s) Skipped
                                    </h3>
                                    <div class="bg-warning/10 border border-warning/30 rounded-lg p-4 max-h-64 overflow-y-auto">
                                        <ul class="space-y-1">
                                            @foreach(session('import_errors') as $error)
                                                <li class="text-sm text-warning-content flex items-start gap-2">
                                                    <span class="text-warning mt-0.5">•</span>
                                                    {{ $error }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @else
                                <div class="text-sm text-success flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" />
                                    </svg>
                                    All rows were imported without errors.
                                </div>
                            @endif
                        @else
                            {{-- Empty State --}}
                            <div class="flex flex-col items-center justify-center py-16 text-center text-base-content/40">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-4 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-lg font-medium mb-1">No import run yet</p>
                                <p class="text-sm">Upload a CSV file on the left to import DA reference price data.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Info Card --}}
            <div class="card bg-info/10 border border-info/30 mt-4">
                <div class="card-body py-4">
                    <div class="flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-info mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-sm text-info-content">
                            <p class="font-semibold mb-1">How imported prices are used</p>
                            <p class="text-info-content/70">
                                Imported prices are tagged as <span class="badge badge-ghost badge-sm font-mono">admin_import</span> and are used as reference baseline data for the
                                <a href="{{ route('forecast.index') }}" class="link link-info">Price Forecasting</a> feature.
                                They do not appear as buyer shop prices on the map or in buyer reports.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
