@extends('layouts.admin')

@section('title', 'کانفیگ‌ها')
@section('page_title', 'کانفیگ‌ها')

@section('content')

    @php
        $internetTypes = [
            1 => 'همراه اول',
            2 => 'ایرانسل',
            3 => 'اینترنت خانگی',
        ];

        $accountTypes = [
            1 => 'عادی',
            2 => 'ویژه',
        ];
    @endphp

    <div class="page-header">
        <div>
            <h1>کانفیگ‌ها</h1>
            <p>مدیریت کانفیگ‌های اتصال</p>
        </div>
    </div>

    @if(session('success'))
        <div class="config-alert success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="config-alert danger">
            {{ $errors->first() }}
        </div>
    @endif


    {{-- فقط Super Admin می‌تواند کانفیگ اضافه کند --}}
    @if(auth()->user()->role->value === 'super_admin')

        <div class="config-card">

            <div class="config-card-header">
                <div>
                    <h3>افزودن کانفیگ</h3>
                    <span>کانفیگ را برای نوع حساب و نوع اینترنت مشخص کنید.</span>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.configs.store') }}">
                @csrf

                <div class="config-form-grid">

                    <div>
                        <label>نوع کانفیگ</label>

                        <select name="account_type" required>
                            <option value="">انتخاب کنید</option>

                            @foreach($accountTypes as $value => $label)
                                <option
                                    value="{{ $value }}"
                                    @selected(old('account_type') == $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    <div>
                        <label>نوع اینترنت</label>

                        <select name="internet_type" required>
                            <option value="">انتخاب کنید</option>

                            @foreach($internetTypes as $value => $label)
                                <option
                                    value="{{ $value }}"
                                    @selected(old('internet_type') == $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    <div>
                        <label>توضیحات</label>

                        <input
                            type="text"
                            name="descriptions"
                            value="{{ old('descriptions') }}"
                            placeholder="مثلاً سرور اصلی"
                            required>
                    </div>


                    <div class="config-form-full">
                        <label>محتوای کانفیگ</label>

                        <textarea
                            name="config"
                            rows="5"
                            dir="ltr"
                            placeholder="کانفیگ را وارد کنید..."
                            required>{{ old('config') }}</textarea>
                    </div>


                    <div class="config-checkbox">
                        <label>
                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                checked>
                            فعال باشد
                        </label>
                    </div>


                    <div class="config-submit">
                        <button type="submit">
                            ذخیره کانفیگ
                        </button>
                    </div>

                </div>

            </form>

        </div>

    @endif


    {{-- لیست کانفیگ‌ها --}}

    <div class="config-list">

        @forelse($configs as $config)

            <div class="config-item">

                <div class="config-item-main">

                    <div class="config-item-title">
                        {{ $accountTypes[$config->account_type] ?? 'نامشخص' }}

                        <span class="config-badge">
                            {{ $internetTypes[$config->internet_type] ?? 'نامشخص' }}
                        </span>
                    </div>

                    <div class="config-description">
                        {{ $config->descriptions }}
                    </div>

                    <div class="config-status {{ $config->is_active ? 'active' : 'inactive' }}">
                        {{ $config->is_active ? 'فعال' : 'غیرفعال' }}
                    </div>

                </div>


                <div class="config-item-action">

                    <a
                        href="{{ route('admin.configs.show', $config) }}"
                        class="config-view-button">
                        مشاهده
                    </a>

                </div>

            </div>

        @empty

            <div class="config-empty">
                هنوز کانفیگی ثبت نشده است.
            </div>

        @endforelse

    </div>


    @if($configs->hasPages())
        <div class="config-pagination">
            {{ $configs->links() }}
        </div>
    @endif

@endsection
