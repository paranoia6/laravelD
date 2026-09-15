@php
    $user = auth()->user();

    $isSuperAdmin = $user && $user->role?->value === 'super_admin';
    $isAdmin = $user && $user->role?->value === 'admin';

    $menuItems = [
        [
            'title' => 'داشبورد',
            'route' => 'admin.dashboard',
            'icon' => 'dashboard',
            'section' => 'اصلی',
            'roles' => ['admin', 'super_admin'],
        ],

        [
            'title' => 'اکانت‌ها',
            'route' => 'admin.accounts',
            'icon' => 'users',
            'section' => 'اکانت‌ها',
            'roles' => ['admin', 'super_admin'],
        ],

        [
            'title' => 'ایجاد اکانت',
            'route' => 'admin.accounts.create',
            'icon' => 'plus',
            'section' => 'اکانت‌ها',
            'roles' => ['admin', 'super_admin'],
        ],

        [
            'title' => 'پشتیبانی',
            'route' => 'admin.supports',
            'icon' => 'support',
            'section' => 'مدیریت',
            'roles' => ['admin', 'super_admin'],
        ],

        [
            'title' => 'نرم‌افزار',
            'route' => 'admin.software',
            'icon' => 'download',
            'section' => 'مدیریت',
            'roles' => ['admin', 'super_admin'],
        ],

        [
            'title' => 'اعلان‌ها',
            'route' => 'admin.notifications',
            'icon' => 'bell',
            'section' => 'مدیریت',
            'roles' => ['admin', 'super_admin'],
        ],

        [
            'title' => 'تیکت‌ها',
            'route' => 'admin.tickets',
            'icon' => 'ticket',
            'section' => 'مدیریت',
            'roles' => ['admin', 'super_admin'],
        ],

        [
            'title' => 'گزارش‌ها',
            'route' => 'admin.reports',
            'icon' => 'chart',
            'section' => 'مدیریت',
            'roles' => ['admin', 'super_admin'],
        ],

        [
            'title' => 'کیف پول',
            'route' => 'admin.wallet',
            'icon' => 'wallet',
            'section' => 'مدیریت',
            'roles' => ['admin', 'super_admin'],
        ],

        // فقط Super Admin
        [
            'title' => 'مدیریت Adminها',
            'route' => 'admin.admins',
            'icon' => 'shield',
            'section' => 'مدیریت Super Admin',
            'roles' => ['super_admin'],
        ],

        [
            'title' => 'قیمت پلن‌ها',
            'route' => 'admin.plans',
            'icon' => 'wallet',
            'section' => 'مدیریت Super Admin',
            'roles' => ['super_admin'],
        ],

        [
            'title' => 'کانفیگ‌ها',
            'route' => 'admin.configs',
            'icon' => 'server',
            'section' => 'مدیریت Super Admin',
            'roles' => ['super_admin'],
        ],


        [
            'title' => 'گزارش فعالیت‌ها',
            'route' => 'admin.audit-logs',
            'icon' => 'history',
            'section' => 'مدیریت Super Admin',
            'roles' => ['super_admin'],
        ],
    ];
@endphp

<aside class="admin-sidebar">

    <div class="sidebar-header">
        <div class="sidebar-brand">
            <div class="brand-logo">
                D
            </div>

            <div>
                <strong>Doping</strong>
                <small>پنل مدیریت</small>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav">

        @php
            $currentSection = null;
        @endphp

        @foreach($menuItems as $item)

            @if(! $user || ! in_array($user->role?->value, $item['roles'], true))
                @continue
            @endif

            @if($currentSection !== $item['section'])

                @php
                    $currentSection = $item['section'];
                @endphp

                <div class="sidebar-section-title">
                    {{ $currentSection }}
                </div>

            @endif

            <li class="nav-item">

                <a
                    href="{{ route($item['route']) }}"
                    class="nav-link {{ request()->routeIs($item['route'] . '*') ? 'active' : '' }}"
                >

                    <span class="nav-icon">

                        @switch($item['icon'])

                            @case('dashboard')
                                <svg viewBox="0 0 24 24">
                                    <rect x="3" y="3" width="7" height="7" rx="1"/>
                                    <rect x="14" y="3" width="7" height="7" rx="1"/>
                                    <rect x="3" y="14" width="7" height="7" rx="1"/>
                                    <rect x="14" y="14" width="7" height="7" rx="1"/>
                                </svg>
                                @break

                            @case('users')
                                <svg viewBox="0 0 24 24">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                                @break

                            @case('plus')
                                <svg viewBox="0 0 24 24">
                                    <line x1="12" y1="5" x2="12" y2="19"/>
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                </svg>
                                @break

                            @case('support')
                                <svg viewBox="0 0 24 24">
                                    <path d="M21 11.5a8.38 8.38 0 0 1-9 8.5 8.5 8.5 0 0 1-4.6-1.35L3 20l1.35-4.4A8.5 8.5 0 1 1 21 11.5z"/>
                                </svg>
                                @break

                            @case('download')
                                <svg viewBox="0 0 24 24">
                                    <path d="M12 3v12"/>
                                    <path d="m7 10 5 5 5-5"/>
                                    <path d="M5 21h14"/>
                                </svg>
                                @break

                            @case('bell')
                                <svg viewBox="0 0 24 24">
                                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                                    <path d="M10 21h4"/>
                                </svg>
                                @break

                            @case('ticket')
                                <svg viewBox="0 0 24 24">
                                    <path d="M4 5h16v14H4z"/>
                                    <path d="M8 9h8"/>
                                    <path d="M8 13h5"/>
                                </svg>
                                @break

                            @case('chart')
                                <svg viewBox="0 0 24 24">
                                    <line x1="4" y1="19" x2="20" y2="19"/>
                                    <rect x="6" y="10" width="3" height="7" rx="1"/>
                                    <rect x="11" y="6" width="3" height="11" rx="1"/>
                                    <rect x="16" y="3" width="3" height="14" rx="1"/>
                                </svg>
                                @break

                            @case('wallet')
                                <svg viewBox="0 0 24 24">
                                    <path d="M3 7h17a1 1 0 0 1 1 1v11a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1z"/>
                                    <path d="M16 12h5"/>
                                    <circle cx="16" cy="12" r="1"/>
                                    <path d="M5 7V5a2 2 0 0 1 2-2h11"/>
                                </svg>
                                @break

                            @case('shield')
                                <svg viewBox="0 0 24 24">
                                    <path d="M12 3l8 4v5c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7l8-4z"/>
                                </svg>
                                @break

                            @case('server')
                                <svg viewBox="0 0 24 24">
                                    <rect x="3" y="4" width="18" height="6" rx="1"/>
                                    <rect x="3" y="14" width="18" height="6" rx="1"/>
                                    <circle cx="7" cy="7" r="1"/>
                                    <circle cx="7" cy="17" r="1"/>
                                </svg>
                                @break

                            @case('history')
                                <svg viewBox="0 0 24 24">
                                    <path d="M3 12a9 9 0 1 0 3-6.7"/>
                                    <polyline points="3 4 3 10 9 10"/>
                                    <path d="M12 7v5l3 2"/>
                                </svg>
                                @break

                            @default
                                <span>•</span>

                        @endswitch

                    </span>

                    <span class="nav-text">
                        {{ $item['title'] }}
                    </span>

                </a>

            </li>

        @endforeach

    </nav>

</aside>
