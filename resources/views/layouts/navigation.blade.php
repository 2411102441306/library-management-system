<nav x-data="{ open: false }" class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/90 shadow-[0_1px_0_rgba(15,23,42,0.02)] backdrop-blur-xl">
    @php
        $navUser = auth()->user();
        $profileNeedsAttention = $navUser && $navUser->isMember() && count($navUser->missingBorrowerProfileFields()) > 0;
    @endphp

    <div class="mx-auto flex max-w-7xl items-center gap-4 px-6 py-4">
        <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('member.catalog') }}" class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-2xl shadow-sm" style="background:#0EA5E9">
                <i class="ti ti-books text-lg text-white"></i>
            </div>
            <div>
                <div class="text-sm font-semibold text-slate-900">LibraryMS</div>
                <div class="text-xs text-slate-500">Member Portal</div>
            </div>
        </a>

        <div class="hidden flex-1 items-center justify-center md:flex">
            <div class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-slate-50 p-1 shadow-sm">
                @if(auth()->user()->isAdmin())
                    <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" class="rounded-full border-0 px-4 py-2">
                        <span class="inline-flex items-center gap-2">
                            <i class="ti ti-layout-dashboard text-base flex-shrink-0"></i>
                            Dashboard
                        </span>
                    </x-nav-link>
                @else
                    <x-nav-link :href="route('member.catalog')" :active="request()->routeIs('member.catalog')" class="rounded-full border-0 px-4 py-2">
                        <span class="inline-flex items-center gap-2">
                            <i class="ti ti-books text-base flex-shrink-0"></i>
                            Katalog
                        </span>
                    </x-nav-link>
                    <x-nav-link :href="route('member.history')" :active="request()->routeIs('member.history')" class="rounded-full border-0 px-4 py-2">
                        <span class="inline-flex items-center gap-2">
                            <i class="ti ti-clock-hour-4 text-base flex-shrink-0"></i>
                            Riwayat
                        </span>
                    </x-nav-link>
                    <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')" class="rounded-full border-0 px-4 py-2">
                        <span class="inline-flex items-center gap-2">
                            <i class="ti ti-user-circle text-base flex-shrink-0"></i>
                            Profil
                            @if($profileNeedsAttention)
                                <span class="h-2 w-2 flex-shrink-0 rounded-full bg-rose-500 shadow-sm ring-2 ring-white"></span>
                            @endif
                        </span>
                    </x-nav-link>
                @endif
            </div>
        </div>

        <div class="relative ml-auto" x-data="{ open: false }" @click.outside="open = false">
            <button type="button" @click="open = !open" class="flex items-center gap-3 rounded-full border border-slate-200 bg-white px-2 py-1.5 text-left shadow-sm transition hover:-translate-y-0.5 hover:border-sky-200 hover:shadow-md">
                <x-user-avatar :user="Auth::user()" size="sm" class="border border-sky-100 shadow-sm" />
                <div class="hidden sm:block">
                    <div class="text-sm font-semibold text-slate-800">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-slate-500">Member</div>
                </div>
                <i class="ti ti-chevron-down text-base text-slate-400 transition" :class="open ? 'rotate-180' : ''"></i>
            </button>

            <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95 -translate-y-1" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-2 w-60 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl" style="display:none;">
                <div class="border-b border-slate-100 px-4 py-3">
                    <div class="text-sm font-semibold text-slate-900">{{ Auth::user()->name }}</div>
                    <div class="truncate text-xs text-slate-500">{{ Auth::user()->email }}</div>
                </div>

                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-3 text-sm text-slate-700 transition hover:bg-slate-50 hover:text-sky-700">
                    <i class="ti ti-user-circle text-base text-slate-400"></i>
                    <span>Profil Saya</span>
                    @if($profileNeedsAttention)
                        <span class="ml-auto text-xs font-medium text-amber-600">Belum lengkap</span>
                    @endif
                </a>

                <a href="{{ route('member.history') }}" class="flex items-center gap-2.5 px-4 py-3 text-sm text-slate-700 transition hover:bg-slate-50 hover:text-sky-700">
                    <i class="ti ti-clock-hour-4 text-base text-slate-400"></i>
                    <span>Riwayat Peminjaman</span>
                </a>

                <div class="border-t border-slate-100">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-2.5 px-4 py-3 text-sm text-red-600 transition hover:bg-red-50">
                            <i class="ti ti-logout text-base"></i>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden border-t border-slate-200/80 bg-white/95 backdrop-blur-xl sm:hidden">
        <div class="space-y-1 px-2 pb-3 pt-2">
            @if(auth()->user()->isAdmin())
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    <span class="inline-flex items-center gap-2">
                        <i class="ti ti-layout-dashboard text-base flex-shrink-0"></i>
                        Dashboard
                    </span>
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('member.catalog')" :active="request()->routeIs('member.catalog')">
                    <span class="inline-flex items-center gap-2">
                        <i class="ti ti-books text-base flex-shrink-0"></i>
                        Katalog
                    </span>
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('member.history')" :active="request()->routeIs('member.history')">
                    <span class="inline-flex items-center gap-2">
                        <i class="ti ti-clock-hour-4 text-base flex-shrink-0"></i>
                        Riwayat
                    </span>
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                    <span class="inline-flex items-center gap-2">
                        <i class="ti ti-user-circle text-base flex-shrink-0"></i>
                        Profil
                        @if($profileNeedsAttention)
                            <span class="h-2 w-2 rounded-full bg-rose-500 shadow-sm ring-2 ring-white"></span>
                        @endif
                    </span>
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="border-t border-slate-200 px-4 pb-4 pt-4">
            <div>
                <div class="mb-2">
                    <x-user-avatar :user="Auth::user()" size="lg" />
                </div>
                <div class="text-base font-semibold text-slate-800">{{ Auth::user()->name }}</div>
                <div class="text-sm text-slate-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>