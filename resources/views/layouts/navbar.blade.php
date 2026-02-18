<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid">

        <a class="navbar-brand fw-bold text-primary" href="{{ route('dashboard') }}">
            BNI OSB4
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">

            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <!-- DASHBOARD -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                       href="{{ route('dashboard') }}">
                        Dashboard
                    </a>
                </li>

                <!-- MASTER DATA -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('categories.*','items.*') ? 'active' : '' }}"
                       href="#" role="button" data-bs-toggle="dropdown">
                        Master Data
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item"
                               href="{{ route('categories.index') }}">
                                Kategori
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item"
                               href="{{ route('items.all') }}">
                                Master Barang
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- TRANSAKSI -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('transactions.*') ? 'active' : '' }}"
                       href="#" role="button" data-bs-toggle="dropdown">
                        Transaksi
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item"
                               href="{{ route('transactions.in.form') }}">
                                Barang Masuk
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item"
                               href="{{ route('transactions.out.form') }}">
                                Barang Keluar
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- HISTORY -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('history.*') ? 'active' : '' }}"
                       href="#" role="button" data-bs-toggle="dropdown">
                        History
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item"
                               href="{{ route('history.in') }}">
                                History Masuk
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item"
                               href="{{ route('history.out') }}">
                                History Keluar
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- LAPORAN -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('reports.*') ? 'active' : '' }}"
                       href="#" role="button" data-bs-toggle="dropdown">
                        Laporan
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item"
                               href="{{ route('reports.stock') }}">
                                Laporan Stok
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- IMPORT EXCEL -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('excel.*') ? 'active' : '' }}"
                       href="{{ route('excel.form') }}">
                        Import Excel
                    </a>
                </li>

            </ul>

        </div>
    </div>
</nav>
