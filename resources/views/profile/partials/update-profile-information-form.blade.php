<section>
    @php
        $missingFields = $user->missingBorrowerProfileFields();
        $missingDot = fn (string $field, mixed $fallback = null) => blank(old($field, $fallback));
        $completionSteps = [
            'Nama lengkap'     => !empty($user->name),
            'NIK'              => !empty($user->identity_number),
            'Nomor telepon'    => !empty($user->phone),
            'Tempat lahir'     => !empty($user->birth_place),
            'Tanggal lahir'    => !empty($user->birth_date),
            'Alamat'           => !empty($user->address),
            'Foto profil'      => !empty($user->profile_photo_path),
        ];
        $completedCount = collect($completionSteps)->filter()->count();
        $totalCount     = count($completionSteps);
        $percentage     = (int) round(($completedCount / $totalCount) * 100);
    @endphp

    {{-- Flash / incomplete warning --}}
    @if($missingFields)
        <div class="mb-6 flex items-start gap-3 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3.5 text-sm text-amber-900 shadow-sm">
            <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
            <div>
                <span class="font-semibold">Profil belum lengkap.</span>
                Lengkapi <span class="font-medium">{{ implode(', ', $missingFields) }}</span> agar bisa mengajukan peminjaman.
            </div>
        </div>
    @endif

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="grid gap-6 lg:grid-cols-3">

            {{-- ── LEFT COLUMN: avatar + progress ──────────────────── --}}
            <div class="flex flex-col gap-4">

                {{-- Avatar card --}}
                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                    {{-- Gradient header --}}
                    <div class="h-20 w-full" style="background: linear-gradient(135deg, #0EA5E9 0%, #38BDF8 60%, #BAE6FD 100%);"></div>

                    <div class="flex flex-col items-center px-5 pb-5">
                        {{-- Avatar ring --}}
                        <div class="-mt-10 mb-3 ring-4 ring-white rounded-full shadow-md overflow-hidden">
                            <x-user-avatar :user="$user" size="xl" class="h-20 w-20 rounded-full object-cover" />
                        </div>
                        <div class="text-center">
                            <div class="text-base font-bold text-slate-900 leading-tight">{{ $user->name ?: 'Nama belum diisi' }}</div>
                            <div class="mt-0.5 text-xs text-slate-400">{{ $user->email }}</div>
                            <span class="mt-2 inline-block rounded-full bg-sky-100 px-3 py-0.5 text-xs font-semibold text-sky-700">Member</span>
                        </div>
                    </div>
                </div>

                {{-- Upload photo card --}}
                <div
                    class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm space-y-4"
                    x-data="{
                        initialPhotoPreview: @js($user->profile_photo_url),
                        photoPreview: @js($user->profile_photo_url),
                        photoName: null,
                        onPhotoChange(event) {
                            const file = event.target.files?.[0];

                            if (file) {
                                this.photoName = file.name;
                                this.photoPreview = URL.createObjectURL(file);
                                return;
                            }

                            this.photoName = null;
                            this.photoPreview = this.initialPhotoPreview;
                        },
                    }"
                >
                    <div class="flex items-center gap-4">
                        <div class="flex h-20 w-20 flex-shrink-0 items-center justify-center overflow-hidden rounded-3xl bg-slate-100 ring-4 ring-slate-50">
                            <template x-if="photoPreview">
                                <img :src="photoPreview" alt="Preview foto profil" class="h-full w-full bg-white object-contain object-center p-[2px]">
                            </template>
                            <template x-if="!photoPreview">
                                <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-sky-100 to-sky-200 text-sky-700">
                                    <i class="ti ti-camera text-2xl"></i>
                                </div>
                            </template>
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-semibold text-slate-800">Foto Profil</div>
                            <p class="mt-1 text-xs leading-relaxed text-slate-500">
                                Unggah foto yang jelas agar identitas member mudah diverifikasi. Format JPG atau PNG, maksimal 2 MB.
                            </p>
                        </div>
                    </div>

                    <label for="profile_photo" class="flex cursor-pointer items-center gap-3 rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-3 transition hover:border-sky-300 hover:bg-sky-50/60">
                        <span class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-2xl bg-sky-500 text-white shadow-sm transition hover:bg-sky-600">
                            <i class="ti ti-upload text-lg"></i>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-sm font-semibold text-slate-800">Pilih foto baru</span>
                            <span class="block truncate text-xs text-slate-500" x-text="photoName || 'Belum ada file dipilih'"></span>
                        </span>
                        <span class="shrink-0 rounded-full bg-white px-3 py-1 text-xs font-semibold text-sky-700 ring-1 ring-sky-100">Browse</span>
                    </label>

                    <input
                        id="profile_photo"
                        name="profile_photo"
                        type="file"
                        accept="image/*"
                        class="sr-only"
                        x-on:change="onPhotoChange($event)"
                    />
                    <x-input-error class="mt-1" :messages="$errors->get('profile_photo')" />

                    @if($user->profile_photo_path)
                        <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-600 transition-colors hover:border-red-200 hover:bg-red-50 hover:text-red-600">
                            <input type="checkbox" name="remove_profile_photo" value="1"
                                class="rounded border-slate-300 text-sky-600 shadow-sm focus:ring-sky-500">
                            <span>Hapus foto profil saat ini</span>
                        </label>
                    @else
                        <p class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-2.5 text-xs leading-relaxed text-amber-700">
                            Foto profil wajib diisi sebelum bisa mengajukan peminjaman.
                        </p>
                    @endif
                </div>

                {{-- Completion progress card --}}
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-sm font-semibold text-slate-800">Kelengkapan Profil</div>
                        <span class="text-sm font-bold" style="color:#0EA5E9">{{ $percentage }}%</span>
                    </div>

                    {{-- Progress bar --}}
                    <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100 mb-4">
                        <div class="h-full rounded-full transition-all duration-500"
                            style="width: {{ $percentage }}%; background: linear-gradient(90deg, #0EA5E9, #38BDF8);"></div>
                    </div>

                    <ul class="space-y-1.5">
                        @foreach($completionSteps as $label => $done)
                        <li class="flex items-center gap-2 text-xs {{ $done ? 'text-slate-500' : 'text-amber-600' }}">
                            @if($done)
                                <svg class="h-3.5 w-3.5 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                            @else
                                <svg class="h-3.5 w-3.5 flex-shrink-0 text-amber-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 8v4m0 4h.01"/></svg>
                            @endif
                            {{ $label }}
                        </li>
                        @endforeach
                    </ul>
                </div>

            </div>

            {{-- ── RIGHT COLUMN: form fields ────────────────────────── --}}
            <div class="lg:col-span-2 flex flex-col gap-5">

                {{-- Personal info card --}}
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-5 flex items-center gap-3 pb-4 border-b border-slate-100">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl flex-shrink-0" style="background:#E0F2FE">
                            <svg class="h-4 w-4" style="color:#0EA5E9" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-slate-800">Data Pribadi</div>
                            <div class="text-xs text-slate-400">Informasi identitas yang digunakan untuk verifikasi anggota</div>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <div class="mb-1.5 flex items-center gap-2">
                                <x-input-label for="name" :value="__('Nama Asli / Lengkap')" class="text-xs font-semibold text-slate-600" />
                                @if($missingDot('name', $user->name))
                                    <span class="h-2 w-2 rounded-full bg-rose-500 shadow-sm" title="Belum diisi"></span>
                                @endif
                            </div>
                            <x-text-input
                                id="name" name="name" type="text"
                                class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm outline-none transition focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100"
                                :value="old('name', $user->name)"
                                required autofocus autocomplete="name"
                            />
                            <x-input-error class="mt-1.5" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <div class="mb-1.5 flex items-center gap-2">
                                <x-input-label for="identity_number" :value="__('NIK / Nomor Identitas')" class="text-xs font-semibold text-slate-600" />
                                @if($missingDot('identity_number', $user->identity_number))
                                    <span class="h-2 w-2 rounded-full bg-rose-500 shadow-sm" title="Belum diisi"></span>
                                @endif
                            </div>
                            <x-text-input
                                id="identity_number" name="identity_number" type="text"
                                class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm outline-none transition focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100"
                                :value="old('identity_number', $user->identity_number)"
                                required autocomplete="off"
                            />
                            <x-input-error class="mt-1.5" :messages="$errors->get('identity_number')" />
                        </div>

                        <div>
                            <div class="mb-1.5 flex items-center gap-2">
                                <x-input-label for="phone" :value="__('Nomor Telepon')" class="text-xs font-semibold text-slate-600" />
                                @if($missingDot('phone', $user->phone))
                                    <span class="h-2 w-2 rounded-full bg-rose-500 shadow-sm" title="Belum diisi"></span>
                                @endif
                            </div>
                            <x-text-input
                                id="phone" name="phone" type="text"
                                class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm outline-none transition focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100"
                                :value="old('phone', $user->phone)"
                                required autocomplete="tel"
                            />
                            <x-input-error class="mt-1.5" :messages="$errors->get('phone')" />
                        </div>

                        <div>
                            <div class="mb-1.5 flex items-center gap-2">
                                <x-input-label for="birth_place" :value="__('Tempat Lahir')" class="text-xs font-semibold text-slate-600" />
                                @if($missingDot('birth_place', $user->birth_place))
                                    <span class="h-2 w-2 rounded-full bg-rose-500 shadow-sm" title="Belum diisi"></span>
                                @endif
                            </div>
                            <x-text-input
                                id="birth_place" name="birth_place" type="text"
                                class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm outline-none transition focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100"
                                :value="old('birth_place', $user->birth_place)"
                                required autocomplete="address-level2"
                            />
                            <x-input-error class="mt-1.5" :messages="$errors->get('birth_place')" />
                        </div>

                        <div>
                            <div class="mb-1.5 flex items-center gap-2">
                                <x-input-label for="birth_date" :value="__('Tanggal Lahir')" class="text-xs font-semibold text-slate-600" />
                                @if($missingDot('birth_date', optional($user->birth_date)->format('Y-m-d')))
                                    <span class="h-2 w-2 rounded-full bg-rose-500 shadow-sm" title="Belum diisi"></span>
                                @endif
                            </div>
                            <x-text-input
                                id="birth_date" name="birth_date" type="date"
                                class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm outline-none transition focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100"
                                :value="old('birth_date', optional($user->birth_date)->format('Y-m-d'))"
                                required
                            />
                            <x-input-error class="mt-1.5" :messages="$errors->get('birth_date')" />
                        </div>
                    </div>
                </div>

                {{-- Contact info card --}}
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-5 flex items-center gap-3 pb-4 border-b border-slate-100">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl flex-shrink-0" style="background:#E0F2FE">
                            <svg class="h-4 w-4" style="color:#0EA5E9" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-slate-800">Kontak & Alamat</div>
                            <div class="text-xs text-slate-400">Email dan alamat untuk keperluan komunikasi</div>
                        </div>
                    </div>

                    <div class="grid gap-4">
                        <div>
                            <div class="mb-1.5 flex items-center gap-2">
                                <x-input-label for="email" :value="__('Alamat Email')" class="text-xs font-semibold text-slate-600" />
                                @if($missingDot('email', $user->email))
                                    <span class="h-2 w-2 rounded-full bg-rose-500 shadow-sm" title="Belum diisi"></span>
                                @endif
                            </div>
                            <x-text-input
                                id="email" name="email" type="email"
                                class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm outline-none transition focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100"
                                :value="old('email', $user->email)"
                                required autocomplete="username"
                            />
                            <x-input-error class="mt-1.5" :messages="$errors->get('email')" />

                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                <div class="mt-2 flex items-center gap-2 text-sm text-slate-600">
                                    <svg class="h-4 w-4 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                                    <span>Email belum terverifikasi.</span>
                                    <button form="send-verification" class="text-sky-600 underline underline-offset-4 hover:text-sky-700 font-medium text-xs">
                                        Kirim ulang verifikasi
                                    </button>
                                </div>
                                @if (session('status') === 'verification-link-sent')
                                    <p class="mt-1.5 text-xs font-medium text-emerald-600">Tautan verifikasi sudah dikirim ke email kamu.</p>
                                @endif
                            @endif
                        </div>

                        <div>
                            <div class="mb-1.5 flex items-center gap-2">
                                <x-input-label for="address" :value="__('Alamat Lengkap')" class="text-xs font-semibold text-slate-600" />
                                @if($missingDot('address', $user->address))
                                    <span class="h-2 w-2 rounded-full bg-rose-500 shadow-sm" title="Belum diisi"></span>
                                @endif
                            </div>
                            <textarea
                                id="address" name="address" rows="3"
                                placeholder="Jl. Contoh No. 1, Kelurahan, Kecamatan, Kota..."
                                class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm outline-none transition focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100 resize-none"
                            >{{ old('address', $user->address) }}</textarea>
                            <x-input-error class="mt-1.5" :messages="$errors->get('address')" />
                        </div>
                    </div>
                </div>

                {{-- Save button --}}
                <div class="flex items-center justify-between rounded-3xl border border-slate-200 bg-white px-6 py-4 shadow-sm">
                    <div class="text-xs text-slate-400">
                        Perubahan akan langsung tersimpan dan diterapkan ke semua halaman.
                    </div>
                    <div class="flex items-center gap-3">
                        @if (session('status') === 'profile-updated')
                            <p
                                x-data="{ show: true }"
                                x-show="show"
                                x-transition
                                x-init="setTimeout(() => show = false, 2500)"
                                class="flex items-center gap-1.5 text-sm font-medium text-emerald-600"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                Tersimpan
                            </p>
                        @endif
                        <x-primary-button class="rounded-xl px-6 py-2.5 text-sm font-semibold shadow-sm">
                            Simpan Perubahan
                        </x-primary-button>
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="grid gap-2 sm:grid-cols-2 text-xs text-slate-500">
                        <div class="flex items-center gap-2 rounded-2xl bg-slate-50 px-3 py-2">
                            <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                            Perlu dilengkapi
                        </div>
                        <div class="flex items-center gap-2 rounded-2xl bg-emerald-50 px-3 py-2 text-emerald-700">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            Sudah terisi
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
</section>