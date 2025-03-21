<aside id="sidebar-wrapper">
    <div class="sidebar-brand">
        <a href="index.html"> <img alt="image" src="{{ asset('logo.png') }}" class="header-logo" />
            <span class="logo-name">SMK IT</span>
        </a>
    </div>
    <ul class="sidebar-menu">
        <li class="menu-header">Main</li>
        <li class="dropdown {{ Request::is('dashboard') ? 'active' : ''}} ">
            <a href="{{ route('dashboard') }}" class="nav-link"><i
                    class="fas fa-laptop"></i><span>Dashboard</span></a>
        </li>

        @can('attendances.index')
        <li class="dropdown {{ Request::is('attendance*') ? 'active' : ''}} ">
            <a href="{{ route('attendances.index') }}" class="nav-link"><i
                    class="fas fa-calendar-check"></i><span>Absensi</span></a>
        </li>
        @endcan

        @can('subjects.index')
        <li class="dropdown {{ Request::is('subjects*') ? 'active' : ''}} ">
            <a href="{{ route('subjects.index') }}" class="nav-link"><i class="fas fa-book"></i><span>Mata Pelajaran</span></a>
        </li>
        @endcan

        @can('schedules.index')
        <li class="dropdown {{ Request::is('schedules*') ? 'active' : ''}} ">
            <a href="{{ route('schedules.index') }}" class="nav-link"><i class="fas fa-clipboard-list"></i><span>Jadwal Pelajaran</span></a>
        </li>
        @endcan

        @can('class.index')
        <li class="dropdown {{ Request::is('class*') ? 'active' : ''}} ">
            <a href="{{ route('class.index') }}" class="nav-link"><i class="fas fa-building"></i><span>Kelas</span></a>
        </li>
        @endcan

        @if(auth()->user()->can('roles.index') || auth()->user()->can('permission.index') ||
        auth()->user()->can('users.index'))
        <li class="menu-header">Pengaturan User</li>
        @endif

        

        @can('users.index')
        <li class="dropdown">
            <a href="#" class="menu-toggle nav-link has-dropdown"><i class="fas fa-user"></i><span>User</span></a>
            <ul class="dropdown-menu">
                <li class="{{ Request::is('users*') ? 'active' : ''}} ">
                    <a href="{{ route('users.index') }}" class="nav-link"><i class="fas fa-user-plus"></i><span>Tambah User</span></a>
                </li>
                <li class="dropdown {{ Request::is('student*') ? 'active' : ''}} ">
                    <a href="{{ route('user.student') }}" class="nav-link"><i class="fas fa-users"></i><span>Siswa</span></a>
                </li>
                <li class="dropdown {{ Request::is('teacher*') ? 'active' : ''}} ">
                    <a href="{{ route('user.teacher') }}" class="nav-link"><i class="fas fa-chalkboard-teacher"></i><span>Guru</span></a>
                </li>
            </ul>
          </li>
        @endcan
        @can('permissions.index')
            <li class="dropdown {{ Request::is('permissions*') ? 'active' : ''}} ">
                <a href="{{ route('permissions.index') }}" class="nav-link"><i
                        class="fas fa-user-lock"></i><span>Permissions</span></a>
            </li>
        @endcan

        @can('roles.index')
            <li class="dropdown {{ Request::is('roles*') ? 'active' : ''}} ">
                <a href="{{ route('roles.index') }}" class="nav-link"><i class="fas fa-user-cog"></i><span>Role</span></a>
            </li>
        @endcan
    </ul>
</aside>