<!-- Breadcrumb (Dinamis berdasarkan route) -->
@php
    $breadcrumbs = [];

    if (request()->routeIs('admin.settlement.show') && isset($advance)) {
        $breadcrumbs = [
            ['label' => 'All Report', 'route' => route('admin.all-report')],
            ['label' => $advance->code_advance, 'route' => '#'],
        ];
    } elseif (request()->routeIs('admin.all-report*') || request()->routeIs('admin.settlement.*')) {
        $breadcrumbs = [
            ['label' => 'All Report', 'route' => route('admin.all-report')],
        ];
    } elseif (request()->routeIs('admin.advance.*')) {
        $breadcrumbs = [
            ['label' => 'Input Expense', 'route' => route('admin.advance.index')],
        ];
    } elseif (request()->routeIs('admin.report.*')) {
        $breadcrumbs = [
            ['label' => 'Report', 'route' => route('admin.report.index')],
        ];
    } elseif (request()->routeIs('admin.expense-type.*')) {
        $breadcrumbs = [
            ['label' => 'Master Data', 'route' => '#'],
            ['label' => 'Expense Type', 'route' => route('admin.expense-type.index')],
        ];
    } elseif (request()->routeIs('admin.expense-category.*')) {
        $breadcrumbs = [
            ['label' => 'Master Data', 'route' => '#'],
            ['label' => 'Expense Category', 'route' => route('admin.expense-category.index')],
        ];
    } elseif (request()->routeIs('admin.vendor.*')) {
        $breadcrumbs = [
            ['label' => 'Master Data', 'route' => '#'],
            ['label' => 'Vendor', 'route' => route('admin.vendor.index')],
        ];
    } elseif (request()->routeIs('admin.type.*')) {
        $breadcrumbs = [
            ['label' => 'Master Data', 'route' => '#'],
            ['label' => 'Type', 'route' => route('admin.type.index')],
        ];
    }
@endphp

<nav class="navbar navbar-expand-lg bg-white shadow-sm px-4 py-2 fixed-top" style="z-index: 1040; left: 0; right: 0; margin-left: 220px; border-bottom: 1px solid #eaeaea;">
    <!-- Sidebar toggle -->
    <button class="btn btn-sm btn-outline-success toggle-sidebar-btn d-lg-none me-2" id="toggleSidebar">
        <i class="bi bi-list icon-sm"></i>
    </button>

    <!-- Breadcrumbs -->
    <div aria-label="breadcrumb" class="me-auto d-flex align-items-center">
        <ol class="breadcrumb mb-0 bg-white p-0 m-0 small">
            @foreach ($breadcrumbs as $item)
                @if ($loop->last)
                    <li class="breadcrumb-item active fw-semibold" style="color: #1fbf59;" aria-current="page">
                        {{ $item['label'] }}
                    </li>
                @else
                    <li class="breadcrumb-item">
                        @if($item['route'] !== '#')
                            <a href="{{ $item['route'] }}" class="text-decoration-none text-secondary">
                                {{ $item['label'] }}
                            </a>
                        @else
                            <span class="text-muted">{{ $item['label'] }}</span>
                        @endif
                    </li>
                @endif
            @endforeach
        </ol>
    </div>

    <!-- Right side: User Dropdown -->
    <div class="ms-auto d-flex align-items-center">
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="https://ui-avatars.com/api/?name=Admin&background=1FBF59&color=fff" alt="avatar" width="34" height="34" class="rounded-circle me-2 shadow-sm">
                <span class="fw-medium d-none d-md-inline">Admin</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end mt-2" aria-labelledby="dropdownUser">
                <li><span class="dropdown-item-text fw-semibold">Halo, Admin</span></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a href="{{ route('logout') }}" class="dropdown-item text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
