<style>
    #sidebar {
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
        font-size: 0.775rem; /* fs-sm */
        text-decoration: none;
    }

    #sidebar.show {
        transform: translateX(0);
    }

    @media (min-width: 992px) {
        #sidebar {
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
        font-size: 1rem; /* lebih kecil */
    }
</style>

<div id="sidebar" class="bg-white text-dark d-lg-block p-3">
    <div class="d-lg-none text-end mb-2">
        <button class="btn btn-sm btn-outline-secondary" id="closeSidebar">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <h6 class="fw-semibold text-uppercase text-secondary mb-3" style="font-size: 0.8rem;">Admin Panel</h6>

    <ul class="nav flex-column gap-1">
        <li class="nav-item">
            <a href="{{route('admin.all-report')}}" class="sidebar-link {{ request()->routeIs('admin.all-report') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text sidebar-icon"></i>
                All Report
            </a>
        </li>
        <li class="nav-item">
            <a href="{{route('admin.advance.index')}}" class="sidebar-link {{ request()->routeIs('admin.advance.*') ? 'active' : '' }}">
                <i class="bi bi-cash-stack sidebar-icon"></i>
                Advance
            </a>
        </li>
        <li class="nav-item">
            <a href="{{route('admin.settlement.index')}}" class="sidebar-link {{ request()->routeIs('admin.settlement.*') ? 'active' : '' }}">
                <i class="bi bi-receipt sidebar-icon"></i>
                Settlement
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="sidebar-link">
                <i class="bi bi-gear sidebar-icon"></i>
                Pengaturan
            </a>
        </li>
    </ul>
</div>


@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const toggleBtn = document.getElementById("toggleSidebar");
        const sidebar = document.getElementById("sidebar");
        const closeBtn = document.getElementById("closeSidebar");

        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener("click", function () {
                sidebar.classList.add("show");
            });
        }

        if (closeBtn && sidebar) {
            closeBtn.addEventListener("click", function () {
                sidebar.classList.remove("show");
            });
        }
    });
</script>
@endpush

