<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('Manage DA Reference Price Links') }}
        </h2>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 animate-fade-in-up">

        {{-- ================================================================
             LEFT PANEL — Source Link Form
             ================================================================ --}}
        <div class="md:col-span-1 space-y-6">

            {{-- Source Link Form --}}
            <div class="card bg-base-100 shadow-sm border border-base-300 sticky top-24">
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

                        <button type="submit" class="btn btn-secondary w-full mt-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                            Save Links
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ================================================================
             RIGHT PANEL — Link Monitor
             ================================================================ --}}
        <div class="md:col-span-2">
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body p-0">
                    <div class="p-6 border-b border-base-200">
                        <h2 class="card-title text-lg">Current Source Links</h2>
                        <p class="text-sm text-base-content/60 mt-1">
                            Monitor the currently active DA Bantay Presyo source links provided for each crop.
                        </p>
                    </div>

                    <div class="p-0 overflow-x-auto">
                        <table class="table w-full">
                            <thead class="bg-base-200">
                                <tr>
                                    <th>Crop</th>
                                    <th>Current Link</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($crops as $crop)
                                    @php
                                        $link = $sourceLinks['da_price_source_link_' . $crop->id] ?? null;
                                    @endphp
                                    <tr class="hover">
                                        <td class="font-medium">{{ $crop->name }}</td>
                                        <td>
                                            @if($link)
                                                <a href="{{ $link }}" target="_blank" class="link link-primary text-sm flex items-center gap-1">
                                                    {{ Str::limit($link, 40) }}
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                    </svg>
                                                </a>
                                            @else
                                                <span class="text-base-content/40 text-sm italic">No link provided</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($link)
                                                <div class="badge badge-success badge-sm badge-outline">Active</div>
                                            @else
                                                <div class="badge badge-error badge-sm badge-outline">Missing</div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
