<aside id="sidebar" class="sidebar d-flex flex-column p-3">

    {{-- HEADER + TOGGLE --}}
    <div class="sidebar-brand mb-3 text-center">
        <img src="{{ asset('assets/logo/BNI_logo.png') }}"
             alt="BNI"
             class="sidebar-logo mb-2">

        <div class="small text-light opacity-75 mb-2">
            Inventory Management System<br>
            BNI Corporate University<br>
            <span class="opacity-75">OSB4 Unit</span>
        </div>

        <button id="toggleSidebar"
                class="btn btn-sm text-white mt-1"
                type="button"
                title="Perkecil / Perbesar Sidebar">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <hr class="border-secondary opacity-50">

    {{-- MENU (SCROLL AREA) --}}
    <div class="sidebar-scroll">
        <ul class="nav nav-pills flex-column mb-0">

            {{-- HOME --}}
            <li class="nav-item">
                <a href="{{ route('dashboard') }}"
                   class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="icon"><i class="fas fa-house"></i></span>
                    <span class="text">Home</span>
                </a>
            </li>

            {{-- MASTER DATA --}}
            <div class="sidebar-title">Master Data</div>

            <li class="nav-item">
                <a href="{{ route('categories.index') }}"
                   class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <span class="icon"><i class="fas fa-tags"></i></span>
                    <span class="text">Kategori Barang</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('items.all') }}"
                   class="nav-link {{ request()->routeIs('items.*') ? 'active' : '' }}">
                    <span class="icon"><i class="fas fa-box"></i></span>
                    <span class="text">Data Barang</span>
                </a>
            </li>

            {{-- TRANSAKSI BARANG --}}
            <div class="sidebar-title">Transaksi Barang</div>

            <li class="nav-item">
                <a href="{{ route('transactions.in.form') }}"
                   class="nav-link {{ request()->routeIs('transactions.in*') ? 'active' : '' }}">
                    <span class="icon"><i class="fas fa-arrow-down"></i></span>
                    <span class="text">Barang Masuk</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('transactions.out.form') }}"
                   class="nav-link {{ request()->routeIs('transactions.out*') ? 'active' : '' }}">
                    <span class="icon"><i class="fas fa-arrow-up"></i></span>
                    <span class="text">Barang Keluar</span>
                </a>
            </li>

            {{-- PENGELUARAN --}}
            <div class="sidebar-title">Pengeluaran</div>

            <li class="nav-item">
                <a href="{{ route('expense.categories.index') }}"
                   class="nav-link {{ request()->routeIs('expense.categories.*') ? 'active' : '' }}">
                    <span class="icon"><i class="fas fa-layer-group"></i></span>
                    <span class="text">Kategori Pengeluaran</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('expenses.index') }}"
                   class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                    <span class="icon"><i class="fas fa-wallet"></i></span>
                    <span class="text">Data Pengeluaran</span>
                </a>
            </li>

            {{-- RIWAYAT --}}
            <div class="sidebar-title">Riwayat</div>

            <li class="nav-item">
                <a href="{{ route('history.index') }}"
                   class="nav-link {{ request()->routeIs('history.*') ? 'active' : '' }}">
                    <span class="icon"><i class="fas fa-clock-rotate-left"></i></span>
                    <span class="text">Riwayat Transaksi</span>
                </a>
            </li>

            {{-- LAPORAN --}}
            <div class="sidebar-title">Laporan</div>

            <li class="nav-item">
                <a href="{{ route('reports.index') }}"
                   class="nav-link {{ request()->routeIs('reports.index','reports.stock') ? 'active' : '' }}">
                    <span class="icon"><i class="fas fa-chart-column"></i></span>
                    <span class="text">Laporan Stok</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('reports.expenses') }}"
                   class="nav-link {{ request()->routeIs('reports.expenses') ? 'active' : '' }}">
                    <span class="icon"><i class="fas fa-chart-pie"></i></span>
                    <span class="text">Laporan Pengeluaran</span>
                </a>
            </li>

        </ul>
    </div>

    <hr class="border-secondary opacity-50">

    {{-- PROFILE --}}
    <div class="sidebar-profile mt-auto pt-2">
        <div class="dropdown dropup">
            <a href="#"
               class="nav-link d-flex align-items-center dropdown-toggle"
               data-bs-toggle="dropdown">
                <span class="icon">
                    <span class="badge bg-primary rounded-circle p-2">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </span>
                </span>
                <span class="text">
                    {{ Auth::user()->name }}<br>
                    <small class="text-muted">User</small>
                </span>
            </a>

            <ul class="dropdown-menu dropdown-menu-dark shadow w-100">
                <li>
                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                        <i class="fas fa-user me-2"></i> Profile
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item text-danger">
                            <i class="fas fa-right-from-bracket me-2"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>

</aside>
