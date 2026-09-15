@php
    $currentUser = auth()->user();

    $unreadNotifications = $currentUser
        ? $currentUser->unreadNotifications()->count()
        : 0;

    $balance = (int) ($currentUser->balance ?? 0);
    $isSuperAdmin = $currentUser?->role?->value === 'super_admin';
@endphp

<header class="admin-navbar">

    <div class="navbar-right">

        <button
            type="button"
            class="mobile-menu-btn"
            onclick="document.body.classList.toggle('sidebar-open')"
            aria-label="منو"
        >
            <span></span>
            <span></span>
            <span></span>
        </button>

        <div class="navbar-page-title">
            <span class="navbar-page-dot"></span>

            <div>
                <strong>Doping</strong>
                <small>
                    {{ $isSuperAdmin ? 'پنل Super Admin' : 'پنل Admin' }}
                </small>
            </div>
        </div>

    </div>

    <div class="navbar-left">

        @if(!$isSuperAdmin)

            <div class="navbar-balance" title="موجودی">
                <span class="balance-icon">
                    <svg viewBox="0 0 24 24">
                        <rect x="2" y="5" width="20" height="14" rx="2"/>
                        <path d="M2 9h20"/>
                        <circle cx="17" cy="14" r="1"/>
                    </svg>
                </span>

                <span class="balance-info">
                    <small>موجودی</small>
                    <strong>{{ number_format($balance) }} تومان</strong>
                </span>
            </div>

            @if($balance < 100000)
                <span class="low-balance-badge">
                    موجودی کم
                </span>
            @endif

        @endif

        <a
            href="{{ route('admin.notifications') }}"
            class="navbar-notification"
            title="اعلان‌ها"
        >
            <svg viewBox="0 0 24 24">
                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                <path d="M10 21h4"/>
            </svg>

            @if($unreadNotifications > 0)
                <span class="notification-count">
                    {{ $unreadNotifications > 99 ? '99+' : $unreadNotifications }}
                </span>
            @endif
        </a>

        <div class="navbar-user">

            <div class="navbar-avatar">
                {{ mb_substr($currentUser->email ?? 'U', 0, 1) }}
            </div>

            <div class="navbar-user-info">
                <strong>
                    {{ $currentUser->email ?? 'کاربر' }}
                </strong>

                <small>
                    {{ $isSuperAdmin ? 'Super Admin' : 'Admin' }}
                </small>
            </div>

        </div>

        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf

            <button
                type="submit"
                class="navbar-logout"
                title="خروج"
            >
                <svg viewBox="0 0 24 24">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>

                <span>خروج</span>
            </button>
        </form>

    </div>

</header>

<style>
    .admin-navbar {
        min-height: 72px;
        background: #fff;
        border-bottom: 1px solid #efdde7;
        box-shadow: 0 5px 20px rgba(130, 60, 95, .045);

        display: flex;
        align-items: center;
        justify-content: space-between;

        padding: 0 22px;
        box-sizing: border-box;

        position: sticky;
        top: 0;
        z-index: 50;
    }

    .navbar-right,
    .navbar-left {
        display: flex;
        align-items: center;
    }

    .navbar-right {
        gap: 12px;
    }

    .navbar-left {
        gap: 12px;
    }

    .navbar-page-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .navbar-page-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #d95d91;
        box-shadow: 0 0 0 5px #fff0f6;
    }

    .navbar-page-title strong {
        display: block;
        color: #29252a;
        font-size: 15px;
        font-weight: 800;
    }

    .navbar-page-title small {
        display: block;
        margin-top: 2px;
        color: #999;
        font-size: 10px;
    }

    .navbar-balance {
        min-height: 45px;
        padding: 0 12px;

        display: flex;
        align-items: center;
        gap: 9px;

        background: #fff8fb;
        border: 1px solid #efdde7;
        border-radius: 12px;

        color: #29252a;
        text-decoration: none;

        transition: .2s;
    }

    .navbar-balance:hover {
        border-color: #e5b9cd;
        box-shadow: 0 5px 16px rgba(130, 60, 95, .07);
    }

    .balance-icon {
        width: 30px;
        height: 30px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 9px;
        background: #fff0f6;
        color: #a14970;
    }

    .balance-icon svg {
        width: 17px;
        height: 17px;

        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .balance-info small {
        display: block;
        color: #999;
        font-size: 9px;
    }

    .balance-info strong {
        display: block;
        margin-top: 2px;
        color: #a14970;
        font-size: 11px;
    }

    .low-balance-badge {
        padding: 7px 10px;

        border-radius: 999px;

        background: #fff0f3;
        border: 1px solid #f5d7df;

        color: #c43d5d;
        font-size: 10px;
        font-weight: 700;
    }

    .navbar-notification {
        width: 43px;
        height: 43px;

        display: flex;
        align-items: center;
        justify-content: center;

        position: relative;

        background: #fff;
        border: 1px solid #efdde7;
        border-radius: 12px;

        color: #777;
        text-decoration: none;

        transition: .2s;
    }

    .navbar-notification:hover {
        background: #fff8fb;
        color: #a14970;
        box-shadow: 0 5px 16px rgba(130, 60, 95, .07);
    }

    .navbar-notification svg {
        width: 19px;
        height: 19px;

        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .notification-count {
        position: absolute;

        top: -5px;
        left: -5px;

        min-width: 18px;
        height: 18px;

        padding: 0 4px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 999px;

        background: #d95d91;
        color: #fff;

        border: 2px solid #fff;

        font-size: 9px;
        font-weight: 800;
    }

    .navbar-user {
        display: flex;
        align-items: center;
        gap: 9px;

        padding-right: 5px;
    }

    .navbar-avatar {
        width: 39px;
        height: 39px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 11px;

        background: #fff0f6;
        color: #a14970;

        font-size: 14px;
        font-weight: 800;
    }

    .navbar-user-info strong {
        display: block;

        max-width: 180px;

        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;

        color: #444;
        font-size: 11px;
    }

    .navbar-user-info small {
        display: block;
        margin-top: 3px;

        color: #999;
        font-size: 9px;
    }

    .navbar-logout {
        min-height: 42px;

        display: flex;
        align-items: center;
        gap: 7px;

        padding: 0 12px;

        border: 1px solid #f5d7df;
        border-radius: 11px;

        background: #fff0f3;
        color: #c43d5d;

        cursor: pointer;
        font: inherit;
        font-size: 11px;
        font-weight: 700;

        transition: .2s;
    }

    .navbar-logout:hover {
        background: #ffe4eb;
    }

    .navbar-logout svg {
        width: 17px;
        height: 17px;

        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .mobile-menu-btn {
        display: none;

        width: 40px;
        height: 40px;

        border: 1px solid #efdde7;
        border-radius: 10px;

        background: #fff;
        cursor: pointer;

        padding: 9px;
    }

    .mobile-menu-btn span {
        display: block;

        height: 2px;
        margin: 4px 0;

        border-radius: 5px;

        background: #a14970;
    }

    @media (max-width: 900px) {

        .admin-navbar {
            padding: 0 14px;
        }

        .mobile-menu-btn {
            display: block;
        }

        .navbar-user-info {
            display: none;
        }

        .navbar-balance {
            padding: 0 8px;
        }

        .balance-info {
            display: none;
        }

        .low-balance-badge {
            display: none;
        }

        .navbar-logout span {
            display: none;
        }

        .navbar-logout {
            width: 42px;
            padding: 0;
            justify-content: center;
        }
    }

    @media (max-width: 600px) {

        .admin-navbar {
            min-height: 64px;
        }

        .navbar-page-title small {
            display: none;
        }

        .navbar-page-title strong {
            font-size: 13px;
        }

        .navbar-left {
            gap: 7px;
        }

        .navbar-notification,
        .navbar-logout {
            width: 39px;
            height: 39px;
        }

        .navbar-avatar {
            width: 35px;
            height: 35px;
        }
    }
</style>
