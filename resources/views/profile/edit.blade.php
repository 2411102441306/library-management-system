<x-app-layout>
    @php
        $missingFields = auth()->user()->missingBorrowerProfileFields();
        $missingCount = count($missingFields);
    @endphp

    <x-slot name="header">
        <div class="overflow-hidden rounded-3xl border border-sky-200/80 bg-gradient-to-r from-sky-600 via-sky-500 to-cyan-500 px-6 py-6 shadow-lg shadow-sky-100/60 sm:px-8">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                <div class="space-y-2 text-white">
                    <span class="inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-widest text-white/90">
                        Pengaturan akun
                    </span>
                    <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">Profile</h2>
                    <p class="max-w-2xl text-sm leading-6 text-sky-50 sm:text-base">
                        Lengkapi data pribadi agar akun konsisten dipakai di katalog, peminjaman, dan verifikasi anggota.
                    </p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 lg:w-[360px]">
                    <div class="rounded-2xl bg-white/15 px-4 py-3 text-white ring-1 ring-white/15 backdrop-blur">
                        <div class="text-xs uppercase tracking-wide text-white/70">Status</div>
                        <div class="mt-1 flex items-center gap-2 text-sm font-semibold">
                            <span class="h-2.5 w-2.5 rounded-full {{ $missingCount > 0 ? 'bg-rose-300' : 'bg-emerald-300' }}"></span>
                            {{ $missingCount > 0 ? 'Perlu dilengkapi' : 'Sudah lengkap' }}
                        </div>
                    </div>
                    <div class="rounded-2xl bg-white/15 px-4 py-3 text-white ring-1 ring-white/15 backdrop-blur">
                        <div class="text-xs uppercase tracking-wide text-white/70">Data kosong</div>
                        <div class="mt-1 text-sm font-semibold">{{ $missingCount }} field</div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="profile-shell p-6 sm:p-8">
            <div class="grid gap-6 lg:grid-cols-[1fr_320px] lg:items-start">
                <div class="max-w-3xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
                <aside class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="mb-4 flex items-center gap-2 border-b border-slate-100 pb-3">
                        <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-xl" style="background:#E0F2FE">
                            <svg class="h-4 w-4" style="color:#0EA5E9" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z"/>
                            </svg>
                        </div>
                        <div class="text-sm font-semibold text-slate-800">Panduan Profil</div>
                    </div>

                    <ul class="space-y-3 text-sm text-slate-500">
                        <li class="flex items-start gap-2">
                            <span class="mt-1 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-sky-400"></span>
                            Gunakan nama asli agar identitas buku jelas.
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-1 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-sky-400"></span>
                            Isi NIK, telepon, dan alamat supaya bisa verifikasi anggota.
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-1 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-sky-400"></span>
                            Upload foto profil agar tampil konsisten di seluruh halaman.
                        </li>
                    </ul>

                    <div class="mt-4 grid grid-cols-2 gap-3 border-t border-slate-100 pt-3 text-xs">
                        <div class="rounded-xl bg-slate-50 px-3 py-2.5">
                            <div class="mb-0.5 text-slate-400">Fokus</div>
                            <div class="font-semibold text-slate-700">Data pribadi</div>
                        </div>
                        <div class="rounded-xl bg-sky-50 px-3 py-2.5">
                            <div class="mb-0.5 text-slate-400">Target</div>
                            <div class="font-semibold" style="color:#0EA5E9">Profil lengkap</div>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section class="profile-shell p-6 sm:p-8">
            <div class="max-w-3xl">
                @include('profile.partials.update-password-form')
            </div>
        </section>

        <section class="profile-shell p-6 sm:p-8">
            <div class="max-w-3xl">
                @include('profile.partials.delete-user-form')
            </div>
        </section>
    </div>
</x-app-layout>
