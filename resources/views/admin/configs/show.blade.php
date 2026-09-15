@extends('layouts.admin')

@section('title', 'مشاهده کانفیگ')
@section('page_title', 'مشاهده کانفیگ')

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
            <h1>کانفیگ</h1>
            <p>{{ $config->descriptions }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="config-alert success">
            {{ session('success') }}
        </div>
    @endif

    <div class="config-card">

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

        @if(auth()->user()->role->value === 'super_admin')

            <form
                method="POST"
                action="{{ route('admin.configs.update', $config) }}"
                style="margin-top: 20px;">

                @csrf
                @method('PUT')

                <div class="config-form-grid">

                    <div>
                        <label>نوع کانفیگ</label>

                        <select name="account_type" required>
                            @foreach($accountTypes as $value => $label)
                                <option
                                    value="{{ $value }}"
                                    @selected($config->account_type == $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label>نوع اینترنت</label>

                        <select name="internet_type" required>
                            @foreach($internetTypes as $value => $label)
                                <option
                                    value="{{ $value }}"
                                    @selected($config->internet_type == $value)>
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
                            value="{{ $config->descriptions }}"
                            required>
                    </div>

                    <div class="config-form-full">
                        <label>محتوای کانفیگ</label>

                        <textarea
                            name="config"
                            rows="8"
                            dir="ltr"
                            required>{{ $config->config }}</textarea>
                    </div>

                    <div class="config-checkbox">
                        <label>
                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                @checked($config->is_active)>
                            فعال باشد
                        </label>
                    </div>

                    <div class="config-submit">
                        <button type="submit">
                            ذخیره تغییرات
                        </button>
                    </div>

                </div>

            </form>

            <form
                method="POST"
                action="{{ route('admin.configs.destroy', $config) }}"
                onsubmit="return confirm('این کانفیگ حذف شود؟');"
                style="margin-top: 12px;">

                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="config-delete-button">
                    حذف کانفیگ
                </button>

            </form>

        @endif

    </div>

@endsection
