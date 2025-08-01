<style>
    .sidebar {
        width: 220px;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        background-color: #ffffff;
        z-index: 1055;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        box-shadow: 2px 0 8px rgba(0, 0, 0, 0.05);
        border-right: 1px solid #eaeaea;
        font-size: 0.775rem;
        overflow-y: auto;
    }

    .sidebar.show {
        transform: translateX(0);
    }

    /* Sidebar always visible on large screens */
    @media (min-width: 992px) {
        .sidebar {
            transform: translateX(0) !important;
        }
    }
    @media (max-width: 991.98px) {
        nav.navbar {
            margin-left: 0 !important;
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
        background-color: #f1f9f3;
        color: #1fbf59;
    }

    .sidebar-icon {
        margin-right: 0.5rem;
        font-size: 1rem;
    }

    .sidebar-title {
        font-size: 0.8rem;
    }

    .rotate-180 {
        transform: rotate(180deg);
        transition: transform 0.2s ease;
    }

    /* Overlay for mobile */
    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1035;
        display: none;
    }

    @media (min-width: 992px) {
        .sidebar-overlay {
            display: none !important;
        }
    }
</style>

<!-- Sidebar Overlay (for mobile) -->
<div id="sidebarOverlay" class="sidebar-overlay"></div>

<!-- Sidebar -->
<div id="sidebar" class="sidebar px-3">
    <!-- Tombol close (hanya tampil di mobile) -->
    <div class="d-lg-none text-end mb-2">
        <button class="btn btn-sm btn-outline-secondary" id="closeSidebar">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <!-- Logo perusahaan -->
    <div class="mb-2 text-center mt-2">
        <img src="{{ asset('images/LOGO-AVI-OFFICIAL.png') }}" alt="Company Logo" style="max-height: 40px;">
    </div>

    <!-- Logo aplikasi -->
    <div class="mb-4 text-center">
        <span style="color: #1FBF59; font-weight: 600; font-size: 16px;">
            Expense Management
        </span>
    </div>


    <!-- Menu -->
    <ul class="nav flex-column gap-1">
        <li class="nav-item">
            <a href="{{ route('admin.all-report') }}" 
               class="sidebar-link {{ request()->routeIs('admin.all-report*') || request()->routeIs('admin.settlement.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text sidebar-icon"></i>
                All Report
            </a>
        </li>
        {{-- Report Menu --}}
        <li class="nav-item">
            <a class="sidebar-link d-flex justify-content-between align-items-center 
                {{ request()->routeIs('admin.report.expense-type.*') || request()->routeIs('admin.report.vendor.*') ? 'active' : '' }}" 
                data-bs-toggle="collapse" href="#reportMenu" role="button" 
                aria-expanded="{{ request()->routeIs('admin.report.expense-type.*') || request()->routeIs('admin.report.vendor.*') ? 'true' : 'false' }}" 
                aria-controls="reportMenu">
                <div>
                    <i class="bi bi-clipboard-data sidebar-icon"></i>
                    Report
                </div>
                <i class="bi bi-chevron-down small toggle-icon 
                    {{ request()->routeIs('admin.report.expense-type.*') || request()->routeIs('admin.report.vendor.*') ? 'rotate-180' : '' }}"></i>
            </a>
            <div class="collapse 
                {{ request()->routeIs('admin.report.expense-type.*') || request()->routeIs('admin.report.vendor.*') ? 'show' : '' }}" 
                id="reportMenu">
                <ul class="nav flex-column ms-3 mt-1 small">
                    <li class="nav-item">
                        <a href="{{ route('admin.report.expense-type.index') }}" 
                        class="sidebar-link {{ request()->routeIs('admin.report.expense-type.*') ? 'active' : '' }}">
                            <i class="bi bi-pie-chart sidebar-icon"></i>
                            By Expense Type
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.report.vendor.index') }}" 
                        class="sidebar-link {{ request()->routeIs('admin.report.vendor.*') ? 'active' : '' }}">
                            <i class="bi bi-building sidebar-icon"></i>
                            By Vendor
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        {{-- Master Data Menu --}}
        <li class="nav-item">
            <a class="sidebar-link d-flex justify-content-between align-items-center {{ request()->routeIs('admin.expense-type.*') || request()->routeIs('admin.expense-category.*') || request()->routeIs('admin.vendor.*') || request()->routeIs('admin.type.*') ? 'active' : '' }}" 
            data-bs-toggle="collapse" href="#masterDataMenu" role="button" aria-expanded="{{ request()->routeIs('admin.expense-type.*') || request()->routeIs('admin.expense-category.*') || request()->routeIs('admin.vendor.*') || request()->routeIs('admin.type.*') ? 'true' : 'false' }}" 
            aria-controls="masterDataMenu">
                <div>
                    <i class="bi bi-folder sidebar-icon"></i>
                    Master Data
                </div>
                <i class="bi bi-chevron-down small toggle-icon {{ request()->routeIs('admin.expense-type.*') || request()->routeIs('admin.expense-category.*') || request()->routeIs('admin.vendor.*') || request()->routeIs('admin.type.*') ? 'rotate-180' : '' }}"></i>
            </a>
            <div class="collapse {{ request()->routeIs('admin.expense-type.*') || request()->routeIs('admin.expense-category.*') || request()->routeIs('admin.vendor.*') || request()->routeIs('admin.type.*') ? 'show' : '' }}" id="masterDataMenu">
                <ul class="nav flex-column ms-3 mt-1 small">
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
                    <li class="nav-item">
                        <a href="{{ route('admin.type.index') }}" 
                        class="sidebar-link {{ request()->routeIs('admin.type.*') ? 'active' : '' }}">
                            <i class="bi bi-bookmark sidebar-icon"></i>
                            Type
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.vendor.index') }}" 
                        class="sidebar-link {{ request()->routeIs('admin.vendor.*') ? 'active' : '' }}">
                            <i class="bi bi-house-gear sidebar-icon"></i>
                            Vendor
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
            const sidebarOverlay = document.getElementById("sidebarOverlay");

            // Toggle sidebar on mobile
            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener("click", () => {
                    sidebar.classList.add("show");
                    sidebarOverlay.style.display = "block";
                });
            }

            // Close sidebar on mobile
            if (closeBtn && sidebar) {
                closeBtn.addEventListener("click", () => {
                    sidebar.classList.remove("show");
                    sidebarOverlay.style.display = "none";
                });
            }

            // Close sidebar when clicking on overlay
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener("click", () => {
                    sidebar.classList.remove("show");
                    sidebarOverlay.style.display = "none";
                });
            }

           // Fungsi untuk handle toggle icon rotasi pada sidebar collapse
            function handleSidebarToggle(toggleSelector, collapseId) {
                const toggleLink = document.querySelector(`[href="#${collapseId}"]`);
                const collapseEl = document.getElementById(collapseId);

                if (toggleLink && collapseEl) {
                    const icon = toggleLink.querySelector('.toggle-icon');

                    collapseEl.addEventListener('show.bs.collapse', () => {
                        icon.classList.add('rotate-180');
                    });

                    collapseEl.addEventListener('hide.bs.collapse', () => {
                        icon.classList.remove('rotate-180');
                    });
                }
            }

            // Inisialisasi untuk masing-masing menu collapse
            handleSidebarToggle('[href="#masterDataMenu"]', 'masterDataMenu');
            handleSidebarToggle('[href="#reportMenu"]', 'reportMenu');

        });
    </script>
@endpush