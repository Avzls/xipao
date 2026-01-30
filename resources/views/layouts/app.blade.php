<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Xipao') - Sistem Manajemen Stok</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background min-h-screen overflow-x-hidden" x-data="{ sidebarOpen: false, sidebarMinimized: false }">
    <div class="flex">
        <!-- Mobile Overlay -->
        <div 
            x-show="sidebarOpen" 
            @click="sidebarOpen = false" 
            class="fixed inset-0 bg-black/50 z-40 lg:hidden"
            x-transition
        ></div>

        <!-- Sidebar -->
        <aside 
            class="fixed left-0 top-0 h-full bg-primary-600 text-white z-50 flex flex-col transition-all duration-300"
            :class="{
                'w-64': !sidebarMinimized,
                'w-16': sidebarMinimized,
                '-translate-x-full lg:translate-x-0': !sidebarOpen,
                'translate-x-0': sidebarOpen
            }"
        >
            <!-- Logo -->
            <div class="px-4 py-4 border-b border-primary-700 flex items-center justify-between">
                <div class="flex items-center gap-2" x-show="!sidebarMinimized">
                    <span class="text-2xl">🥟</span>
                    <div>
                        <h1 class="text-lg font-bold text-white">Xipao</h1>
                        <p class="text-xs text-white/60">Sistem Stok</p>
                    </div>
                </div>
                <span class="text-2xl" x-show="sidebarMinimized">🥟</span>
                <button @click="sidebarMinimized = !sidebarMinimized" class="hidden lg:block p-1 text-white/60 hover:text-white">
                    <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" :class="{'rotate-180': sidebarMinimized}">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="mt-2 px-2 flex-1 overflow-y-auto">
                <a href="{{ route('dashboard') }}" class="sidebar-link" :class="{ 'justify-center': sidebarMinimized, 'active': {{ request()->routeIs('dashboard') ? 'true' : 'false' }} }" title="Dashboard">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span x-show="!sidebarMinimized">Dashboard</span>
                </a>

                <div class="mt-4 mb-1 px-2 text-xs font-semibold text-white/40 uppercase" x-show="!sidebarMinimized">Warung</div>
                <a href="{{ route('warung.index') }}" class="sidebar-link" :class="{ 'justify-center': sidebarMinimized, 'active': {{ request()->routeIs('warung.index') || request()->routeIs('warung.create') || request()->routeIs('warung.edit') ? 'true' : 'false' }} }" title="Daftar Warung">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span x-show="!sidebarMinimized">Daftar Warung</span>
                </a>

                <div class="mt-4 mb-1 px-2 text-xs font-semibold text-white/40 uppercase" x-show="!sidebarMinimized">Stok</div>
                <a href="{{ route('stok.index') }}" class="sidebar-link" :class="{ 'justify-center': sidebarMinimized, 'active': {{ request()->routeIs('stok.*') ? 'true' : 'false' }} }" title="Stock Besar">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span x-show="!sidebarMinimized">Stok Besar</span>
                </a>

                <div class="mt-4 mb-1 px-2 text-xs font-semibold text-white/40 uppercase" x-show="!sidebarMinimized">Transaksi</div>
                <a href="{{ route('transaksi.index') }}" class="sidebar-link" :class="{ 'justify-center': sidebarMinimized, 'active': {{ request()->routeIs('transaksi.*') ? 'true' : 'false' }} }" title="Transaksi">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span x-show="!sidebarMinimized">Transaksi</span>
                </a>
                <a href="{{ route('operasional.index') }}" class="sidebar-link" :class="{ 'justify-center': sidebarMinimized, 'active': {{ request()->routeIs('operasional.*') ? 'true' : 'false' }} }" title="Operasional">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span x-show="!sidebarMinimized">Operasional</span>
                </a>

                <a href="{{ route('laporan.konsolidasi') }}" class="sidebar-link" :class="{ 'justify-center': sidebarMinimized, 'active': {{ request()->routeIs('laporan.*') ? 'true' : 'false' }} }" title="Konsolidasi">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span x-show="!sidebarMinimized">Laporan</span>
                </a>
            </nav>

            <!-- User Info -->
            <div class="p-3 border-t border-primary-700 bg-primary-800">
                <div class="flex items-center gap-2" :class="{ 'justify-center': sidebarMinimized }">
                    <div class="w-8 h-8 rounded-full bg-primary-700 flex items-center justify-center text-white font-bold text-sm shrink-0">
                        {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0" x-show="!sidebarMinimized">
                        <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" x-show="!sidebarMinimized">
                        @csrf
                        <button type="submit" class="p-1.5 text-white/60 hover:text-white" title="Logout">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 transition-all duration-300 min-h-screen min-w-0" :class="{ 'lg:ml-64': !sidebarMinimized, 'lg:ml-16': sidebarMinimized }">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm border-b border-secondary-300 px-4 lg:px-6 py-3 sticky top-0 z-30">
                <div class="flex items-center justify-between gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 text-text-secondary hover:text-text-primary">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-lg lg:text-xl font-semibold text-text-primary truncate">@yield('page-title', 'Dashboard')</h2>
                        <p class="text-xs lg:text-sm text-text-secondary hidden sm:block">@yield('page-subtitle', '')</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        @yield('header-actions')
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="p-4 lg:p-6">
                @if(session('success'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            showSuccess('{{ session('success') }}');
                        });
                    </script>
                @endif

                @if(session('error'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            showError('{{ session('error') }}');
                        });
                    </script>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <!-- Custom Confirm Dialog -->
    <div id="customConfirmDialog" class="fixed inset-0 z-[100] hidden">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" id="confirmOverlay"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div id="confirmBox" class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform scale-95 opacity-0 transition-all duration-200">
                <!-- Header with Icon -->
                <div class="p-6 pb-4">
                    <div id="confirmIconWrapper" class="w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4">
                        <!-- Icon will be set by JS -->
                    </div>
                    <h3 id="confirmTitle" class="text-xl font-bold text-center text-gray-800"></h3>
                </div>
                
                <!-- Message -->
                <div class="px-6 pb-6">
                    <p id="confirmMessage" class="text-center text-gray-600"></p>
                </div>
                
                <!-- Buttons -->
                <div class="p-4 bg-gray-50 rounded-b-2xl flex gap-3">
                    <button id="confirmCancel" class="flex-1 px-4 py-3 rounded-xl font-semibold text-gray-700 bg-gray-200 hover:bg-gray-300 transition-colors">
                        Batal
                    </button>
                    <button id="confirmOk" class="flex-1 px-4 py-3 rounded-xl font-semibold text-white transition-colors">
                        Ya, Lanjutkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Custom Confirm Dialog
    function showConfirm(options) {
        return new Promise((resolve) => {
            const dialog = document.getElementById('customConfirmDialog');
            const confirmBox = document.getElementById('confirmBox');
            const iconWrapper = document.getElementById('confirmIconWrapper');
            const title = document.getElementById('confirmTitle');
            const message = document.getElementById('confirmMessage');
            const okBtn = document.getElementById('confirmOk');
            const cancelBtn = document.getElementById('confirmCancel');
            
            // Set type styling
            const type = options.type || 'warning';
            const styles = {
                warning: { bg: 'bg-amber-100', icon: '⚠️', btnBg: 'bg-amber-500 hover:bg-amber-600' },
                danger: { bg: 'bg-red-100', icon: '🗑️', btnBg: 'bg-red-500 hover:bg-red-600' },
                info: { bg: 'bg-blue-100', icon: '❓', btnBg: 'bg-blue-500 hover:bg-blue-600' },
                success: { bg: 'bg-emerald-100', icon: '✅', btnBg: 'bg-emerald-500 hover:bg-emerald-600' }
            };
            
            const style = styles[type] || styles.warning;
            iconWrapper.className = `w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4 ${style.bg}`;
            iconWrapper.innerHTML = `<span class="text-3xl">${options.icon || style.icon}</span>`;
            title.textContent = options.title || 'Konfirmasi';
            message.textContent = options.message || 'Apakah Anda yakin?';
            okBtn.className = `flex-1 px-4 py-3 rounded-xl font-semibold text-white transition-colors ${style.btnBg}`;
            okBtn.textContent = options.confirmText || 'Ya, Lanjutkan';
            cancelBtn.textContent = options.cancelText || 'Batal';
            
            // Show dialog with animation
            dialog.classList.remove('hidden');
            requestAnimationFrame(() => {
                confirmBox.classList.remove('scale-95', 'opacity-0');
                confirmBox.classList.add('scale-100', 'opacity-100');
            });
            
            // Handle buttons
            const cleanup = () => {
                confirmBox.classList.remove('scale-100', 'opacity-100');
                confirmBox.classList.add('scale-95', 'opacity-0');
                setTimeout(() => dialog.classList.add('hidden'), 200);
            };
            
            okBtn.onclick = () => { cleanup(); resolve(true); };
            cancelBtn.onclick = () => { cleanup(); resolve(false); };
            document.getElementById('confirmOverlay').onclick = () => { cleanup(); resolve(false); };
        });
    }
    
    // Alias for delete confirmation
    function confirmDelete(itemName) {
        return showConfirm({
            type: 'danger',
            icon: '🗑️',
            title: 'Hapus Data?',
            message: `Yakin hapus ${itemName || 'data ini'}? Tindakan ini tidak dapat dibatalkan.`,
            confirmText: 'Ya, Hapus',
            cancelText: 'Batal'
        });
    }
    
    // Handle delete with form - simplified
    function handleDelete(form, itemName) {
        if (confirm('Yakin hapus ' + (itemName || 'data ini') + '? Tindakan ini tidak dapat dibatalkan.')) {
            form.submit();
        }
    }
    </script>
</body>
</html>
