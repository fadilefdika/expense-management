<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4 fixed-top" style="z-index: 1040; left: 0; right: 0; margin-left: 220px;">
    <!-- Sidebar toggle -->
    <button class="btn btn-outline-secondary btn-sm d-lg-none me-2 py-1 px-2" id="toggleSidebar">
        <i class="bi bi-list fs-6"></i>
    </button>

    <!-- Brand -->
    <a class="navbar-brand fw-bold" href="#">AdminPanel</a>

    <!-- Right side -->
    <div class="ms-auto d-flex align-items-center">
        <!-- Dropdown -->
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="https://ui-avatars.com/api/?name=Admin" alt="avatar" width="32" height="32" class="rounded-circle me-2">
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownUser">
                <li><span class="dropdown-item-text fw-semibold">Halo, Admin</span></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a href="{{ route('logout') }}" class="dropdown-item text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>