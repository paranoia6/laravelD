@extends('layouts.admin')

@section('title', 'داشبورد')
@section('header', 'داشبورد')

@section('content')

    <!-- Page Header -->
    <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <div class="flex items-center gap-2 text-sm text-slate-400">
                <span>داشبورد</span>
                <span>‹</span>
                <span class="text-slate-600">مدیریت سیستم</span>
            </div>

            <h1 class="mt-2 text-xl font-semibold text-slate-800">
                داشبورد مدیریت
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                نمای کلی وضعیت کاربران، اکانت‌ها و فروش
            </p>
        </div>

    </div>


    <!-- Statistics -->
    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">

        <!-- Customers -->
        <div class="rounded-md border border-slate-200 bg-white p-5">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm text-slate-500">
                        کل مشتریان
                    </p>

                    <h2 class="mt-3 text-2xl font-semibold text-slate-800">
                        ۱٬۲۵۸
                    </h2>

                    <p class="mt-3 text-xs text-slate-500">
                        نسبت به هفته گذشته
                        <span class="mr-1 rounded bg-emerald-100 px-1.5 py-1 text-emerald-600">
                            +۲۹
                        </span>
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-50 text-indigo-600">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-6 w-6"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="1.8"
                              d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM22 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                    </svg>
                </div>

            </div>

        </div>


        <!-- Shopkeepers -->
        <div class="rounded-md border border-slate-200 bg-white p-5">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm text-slate-500">
                        مغازه‌داران
                    </p>

                    <h2 class="mt-3 text-2xl font-semibold text-slate-800">
                        ۸۶
                    </h2>

                    <p class="mt-3 text-xs text-slate-500">
                        نمایندگان فعال
                        <span class="mr-1 rounded bg-emerald-100 px-1.5 py-1 text-emerald-600">
                            ۷۹ فعال
                        </span>
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-6 w-6"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="1.8"
                              d="M3 21h18M5 21V8l7-5 7 5v13M9 21v-6h6v6M9 9h.01M15 9h.01"/>
                    </svg>
                </div>

            </div>

        </div>


        <!-- Accounts -->
        <div class="rounded-md border border-slate-200 bg-white p-5">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm text-slate-500">
                        اکانت‌های ساخته‌شده
                    </p>

                    <h2 class="mt-3 text-2xl font-semibold text-slate-800">
                        ۶٬۲۵۸
                    </h2>

                    <p class="mt-3 text-xs text-slate-500">
                        مجموع اکانت‌های سیستم
                        <span class="mr-1 rounded bg-indigo-100 px-1.5 py-1 text-indigo-600">
                            فعال
                        </span>
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-6 w-6"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="1.8"
                              d="M12 11c3.314 0 6-1.79 6-4s-2.686-4-6-4-6 1.79-6 4 2.686 4 6 4zM3 21c0-3.314 4.03-6 9-6s9 2.686 9 6"/>
                    </svg>
                </div>

            </div>

        </div>


        <!-- Remaining -->
        <div class="rounded-md border border-slate-200 bg-white p-5">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm text-slate-500">
                        اکانت‌های باقی‌مانده
                    </p>

                    <h2 class="mt-3 text-2xl font-semibold text-slate-800">
                        ۱٬۴۳۲
                    </h2>

                    <p class="mt-3 text-xs text-slate-500">
                        قابل ساخت توسط مغازه‌داران
                        <span class="mr-1 rounded bg-amber-100 px-1.5 py-1 text-amber-600">
                            موجود
                        </span>
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-amber-50 text-amber-600">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-6 w-6"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="1.8"
                              d="M20 7l-8-4-8 4m16 0v10l-8 4-8-4V7m16 0l-8 4m-8-4l8 4m0 0v10"/>
                    </svg>
                </div>

            </div>

        </div>

    </div>


    <!-- Main Overview -->
    <div class="mt-6 grid gap-6 xl:grid-cols-2">

        <!-- Account Overview -->
        <div class="rounded-md border border-slate-200 bg-white">

            <div class="border-b border-slate-100 p-5">

                <div class="flex items-center justify-between">

                    <div>
                        <h2 class="font-semibold text-slate-800">
                            وضعیت اکانت‌ها
                        </h2>

                        <p class="mt-1 text-xs text-slate-400">
                            نمای کلی اکانت‌های سرویس
                        </p>
                    </div>

                    <span class="rounded bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-600">
                        این ماه
                    </span>

                </div>

            </div>


            <div class="p-5">

                <div class="flex items-center justify-center">

                    <!-- Donut -->
                    <div
                        class="relative h-48 w-48 rounded-full"
                        style="background: conic-gradient(#555bc4 0deg 245deg, #7479ce 245deg 305deg, #d5d6ee 305deg 360deg);"
                    >

                        <div class="absolute inset-6 flex items-center justify-center rounded-full bg-white">

                            <div class="text-center">
                                <p class="text-2xl font-semibold text-slate-800">
                                    ۷٬۶۹۰
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    مجموع اکانت‌ها
                                </p>
                            </div>

                        </div>

                    </div>

                </div>


                <div class="mt-6 grid grid-cols-3 gap-4 text-center">

                    <div>
                        <div class="mx-auto mb-2 h-2 w-2 rounded-full bg-indigo-600"></div>

                        <p class="text-lg font-semibold text-slate-800">
                            ۵٬۲۴۰
                        </p>

                        <p class="text-xs text-slate-400">
                            فعال
                        </p>
                    </div>

                    <div>
                        <div class="mx-auto mb-2 h-2 w-2 rounded-full bg-indigo-400"></div>

                        <p class="text-lg font-semibold text-slate-800">
                            ۱٬۰۱۸
                        </p>

                        <p class="text-xs text-slate-400">
                            باقی‌مانده
                        </p>
                    </div>

                    <div>
                        <div class="mx-auto mb-2 h-2 w-2 rounded-full bg-slate-300"></div>

                        <p class="text-lg font-semibold text-slate-800">
                            ۱٬۴۳۲
                        </p>

                        <p class="text-xs text-slate-400">
                            بدون استفاده
                        </p>
                    </div>

                </div>

            </div>

        </div>


        <!-- Plans -->
        <div class="rounded-md border border-slate-200 bg-white">

            <div class="border-b border-slate-100 p-5">

                <div class="flex items-center justify-between">

                    <div>
                        <h2 class="font-semibold text-slate-800">
                            پلن‌های سرویس
                        </h2>

                        <p class="mt-1 text-xs text-slate-400">
                            وضعیت پلن‌های عادی و ویژه
                        </p>
                    </div>

                    <span class="rounded bg-slate-100 px-2.5 py-1 text-xs text-slate-500">
                        مشاهده همه
                    </span>

                </div>

            </div>


            <div class="divide-y divide-slate-100">

                <!-- Normal -->
                <div class="flex items-center justify-between p-5">

                    <div class="flex items-center gap-3">

                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-50 text-indigo-600">
                            N
                        </div>

                        <div>
                            <p class="font-medium text-slate-800">
                                پلن عادی
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                مناسب کاربران معمولی
                            </p>
                        </div>

                    </div>

                    <div class="text-left">

                        <p class="font-semibold text-slate-800">
                            ۳٬۸۵۰
                        </p>

                        <p class="mt-1 text-xs text-emerald-500">
                            فعال
                        </p>

                    </div>

                </div>


                <!-- Special -->
                <div class="flex items-center justify-between p-5">

                    <div class="flex items-center gap-3">

                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-50 text-amber-600">
                            V
                        </div>

                        <div>
                            <p class="font-medium text-slate-800">
                                پلن ویژه
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                مناسب کاربران ویژه
                            </p>
                        </div>

                    </div>

                    <div class="text-left">

                        <p class="font-semibold text-slate-800">
                            ۱٬۳۹۰
                        </p>

                        <p class="mt-1 text-xs text-emerald-500">
                            فعال
                        </p>

                    </div>

                </div>


                <!-- Expired -->
                <div class="flex items-center justify-between p-5">

                    <div class="flex items-center gap-3">

                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-50 text-red-500">
                            !
                        </div>

                        <div>
                            <p class="font-medium text-slate-800">
                                منقضی‌شده
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                نیازمند تمدید
                            </p>
                        </div>

                    </div>

                    <div class="text-left">

                        <p class="font-semibold text-slate-800">
                            ۲۴۵
                        </p>

                        <p class="mt-1 text-xs text-red-500">
                            منقضی
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- Bottom Section -->
    <div class="mt-6 grid gap-6 xl:grid-cols-2">

        <!-- Shopkeepers -->
        <div class="rounded-md border border-slate-200 bg-white">

            <div class="border-b border-slate-100 p-5">

                <div class="flex items-center justify-between">

                    <div>
                        <h2 class="font-semibold text-slate-800">
                            مغازه‌داران
                        </h2>

                        <p class="mt-1 text-xs text-slate-400">
                            آخرین وضعیت نمایندگان
                        </p>
                    </div>

                    <button class="rounded bg-indigo-50 px-3 py-2 text-xs font-medium text-indigo-600">
                        مشاهده همه
                    </button>

                </div>

            </div>


            <div class="divide-y divide-slate-100">

                @foreach([
                    ['مغازه مرکزی', '۱۲۸', 'فعال'],
                    ['فروشگاه اینترنتی', '۸۵', 'فعال'],
                    ['نمایندگی شمال', '۴۲', 'فعال'],
                    ['فروشگاه غرب', '۱۲', 'کم'],
                ] as $shop)

                    <div class="flex items-center justify-between p-5">

                        <div class="flex items-center gap-3">

                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 font-medium text-slate-600">
                                {{ mb_substr($shop[0], 0, 1) }}
                            </div>

                            <div>
                                <p class="font-medium text-slate-800">
                                    {{ $shop[0] }}
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    موجودی: {{ $shop[1] }} اکانت
                                </p>
                            </div>

                        </div>

                        @if($shop[2] === 'فعال')

                            <span class="rounded bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-600">
                                فعال
                            </span>

                        @else

                            <span class="rounded bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-600">
                                موجودی کم
                            </span>

                        @endif

                    </div>

                @endforeach

            </div>

        </div>


        <!-- Recent Activity -->
        <div class="rounded-md border border-slate-200 bg-white">

            <div class="border-b border-slate-100 p-5">

                <div class="flex items-center justify-between">

                    <div>
                        <h2 class="font-semibold text-slate-800">
                            فعالیت‌های اخیر
                        </h2>

                        <p class="mt-1 text-xs text-slate-400">
                            آخرین فعالیت‌های سیستم
                        </p>
                    </div>

                </div>

            </div>


            <div class="divide-y divide-slate-100">

                @foreach([
                    ['اکانت جدید ساخته شد', 'توسط مغازه مرکزی', 'امروز', 'bg-emerald-50 text-emerald-600', '+'],
                    ['مشتری جدید ثبت شد', 'کاربر جدید در سیستم', 'امروز', 'bg-indigo-50 text-indigo-600', '+'],
                    ['پلن ویژه فعال شد', 'برای یک مشتری', 'دیروز', 'bg-amber-50 text-amber-600', '✓'],
                    ['اکانت منقضی شد', 'نیازمند تمدید', 'دیروز', 'bg-red-50 text-red-500', '!'],
                ] as $activity)

                    <div class="flex items-center justify-between p-5">

                        <div class="flex items-center gap-3">

                            <div class="flex h-9 w-9 items-center justify-center rounded-full {{ $activity[3] }}">
                                {{ $activity[4] }}
                            </div>

                            <div>
                                <p class="font-medium text-slate-800">
                                    {{ $activity[0] }}
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    {{ $activity[1] }}
                                </p>
                            </div>

                        </div>

                        <span class="text-xs text-slate-400">
                            {{ $activity[2] }}
                        </span>

                    </div>

                @endforeach

            </div>

        </div>

    </div>

@endsection
