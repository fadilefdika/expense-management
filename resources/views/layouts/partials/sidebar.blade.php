<style>
    .sidebar {
        width: 220px;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        background-color: #ffffff;
        z-index: 1040;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        box-shadow: 2px 0 8px rgba(0, 0, 0, 0.05);
        border-right: 1px solid #eaeaea;
        font-size: 0.775rem;
    }

    .sidebar.show {
        transform: translateX(0);
    }

    /* Sidebar always visible on large screens */
    @media (min-width: 992px) {
        .sidebar {
            transform: translateX(0) !important;
            position: static;
        }
    }

    .sidebar-link {
        display: flex;
        align-items: center;
        padding: 0.5rem 0.75rem;
        border-radius: 0.375rem;
        color: #333;
        text-decoration: none;
        font-weight: 500;
        transition: background-color 0.2s, color 0.2s;
    }

    .sidebar-link:hover,
    .sidebar-link.active {
        background-color: #f1f5f9;
        color: #0d6efd;
    }

    .sidebar-icon {
        margin-right: 0.5rem;
        font-size: 1rem;
    }

    .sidebar-title {
        font-size: 0.8rem;
    }
</style>

<!-- Sidebar -->
<div id="sidebar" class="sidebar p-3">
    <!-- Tombol close (hanya tampil di mobile) -->
    <div class="d-lg-none text-end mb-2">
        <button class="btn btn-sm btn-outline-secondary" id="closeSidebar">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <!-- Judul -->
    <h6 class="fw-semibold text-uppercase text-secondary mb-3 sidebar-title">
        Admin Panel
    </h6>

    <!-- Menu -->
    <ul class="nav flex-column gap-1">
        <li class="nav-item">
            <a href="{{ route('admin.all-report') }}" 
               class="sidebar-link {{ request()->routeIs('admin.all-report*') || request()->routeIs('admin.settlement.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text sidebar-icon"></i>
                All Report
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.advance.index') }}" 
               class="sidebar-link {{ request()->routeIs('admin.advance.*') ? 'active' : '' }}">
                <i class="bi bi-cash-stack sidebar-icon"></i>
                Advance
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.items.index') }}" class="sidebar-link {{ request()->routeIs('admin.items.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam sidebar-icon"></i>
                Item
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.report.index') }}" class="sidebar-link {{ request()->routeIs('admin.report.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard-data sidebar-icon"></i>
                Report
            </a>
        </li>
        {{-- Master Data Menu --}}
        <li class="nav-item">
            <a class="sidebar-link d-flex justify-content-between align-items-center {{ request()->routeIs('admin.expense-type.*') || request()->routeIs('admin.expense-category.*') ? 'active' : '' }}" 
            data-bs-toggle="collapse" href="#masterDataMenu" role="button" aria-expanded="{{ request()->routeIs('admin.expense-type.*') || request()->routeIs('admin.expense-category.*') ? 'true' : 'false' }}" 
            aria-controls="masterDataMenu">
                <div>
                    <i class="bi bi-folder sidebar-icon"></i>
                    Master Data
                </div>
                <i class="bi bi-chevron-down small toggle-icon {{ request()->routeIs('admin.expense-type.*') || request()->routeIs('admin.expense-category.*') ? 'rotate-180' : '' }}"></i>
            </a>
            <div class="collapse {{ request()->routeIs('admin.expense-type.*') || request()->routeIs('admin.expense-category.*') ? 'show' : '' }}" id="masterDataMenu">
                <ul class="nav flex-column ms-3 mt-1">
                    <li class="nav-item">
                        <a href="{{ route('admin.expense-type.index') }}" 
                        class="sidebar-link {{ request()->routeIs('admin.expense-type.*') ? 'active' : '' }}">
                            <i class="bi bi-gear sidebar-icon"></i>
                            Expense Type
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.expense-category.index') }}" 
                        class="sidebar-link {{ request()->routeIs('admin.expense-category.*') ? 'active' : '' }}">
                            <i class="bi bi-tags sidebar-icon"></i>
                            Expense Category
                        </a>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
</div>

 
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const toggleBtn = document.getElementById("toggleSidebar");
        const sidebar = document.getElementById("sidebar");
        const closeBtn = document.getElementById("closeSidebar");

        console.log("DOMContentLoaded triggered");
        console.log("toggleBtn:", toggleBtn);
        console.log("sidebar:", sidebar);
        console.log("closeBtn:", closeBtn);

        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener("click", () => {
                console.log("Toggle button clicked");
                sidebar.classList.add("show");
            });
        } else {
            console.warn("Toggle button or sidebar not found");
        }

        if (closeBtn && sidebar) {
            closeBtn.addEventListener("click", () => {
                console.log("Close button clicked");
                sidebar.classList.remove("show");
            });
        } else {
            console.warn("Close button or sidebar not found");
        }
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const toggleLink = document.querySelector('[href="#masterDataMenu"]');
        const icon = toggleLink.querySelector('.toggle-icon');
        const collapseEl = document.getElementById('masterDataMenu');

        collapseEl.addEventListener('show.bs.collapse', () => {
            icon.classList.add('rotate-180');
        });
        collapseEl.addEventListener('hide.bs.collapse', () => {
            icon.classList.remove('rotate-180');
        });
    });
</script>
@endpush




