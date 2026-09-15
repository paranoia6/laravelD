@extends('layouts.admin')

@section('title', 'نرم‌افزار')
@section('page_title', 'نرم‌افزار')

@section('content')

    ```
    <div class="page-header">
        <div>
            <h1>نرم‌افزار</h1>
            <p>مشاهده و دریافت نرم‌افزار</p>
        </div>
    </div>

    @if(session('success'))
        <div class="software-alert success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="software-alert danger">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- فقط Super Admin می‌تواند نرم‌افزار اضافه کند --}}
    @if(auth()->user()->role->value === 'super_admin')

        <div class="software-card">

            <div class="software-card-header">
                <div>
                    <h3>افزودن نرم‌افزار</h3>
                    <span>لینک دانلود را ثبت کنید تا QR Code آن نیز ساخته شود.</span>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.software.store') }}">
                @csrf

                <div class="software-form-grid">

                    <div>
                        <label>نام نرم‌افزار</label>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="مثلاً Doping VPN"
                            required>
                    </div>

                    <div>
                        <label>لینک دانلود</label>
                        <input
                            type="url"
                            name="download_url"
                            value="{{ old('download_url') }}"
                            placeholder="https://example.com/download"
                            dir="ltr"
                            required>
                    </div>

                    <button type="submit">ذخیره</button>

                </div>
            </form>

        </div>

    @endif


    <div class="software-list">

        @forelse($software as $item)

            <div class="software-item">

                <div class="software-info">

                    <div class="software-icon">APP</div>

                    <div>
                        <h3>{{ $item->name }}</h3>

                        <a
                            href="{{ $item->download_url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="software-download-url"
                            dir="ltr">
                            {{ $item->download_url }}
                        </a>

                        <span class="software-status {{ $item->is_active ? 'active' : 'inactive' }}">
                        {{ $item->is_active ? 'فعال' : 'غیرفعال' }}
                    </span>
                    </div>

                </div>


                {{-- QR برای همه قابل مشاهده است --}}
                @if($item->is_active)

                    <div class="software-qr">
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(130)->margin(1)->generate($item->download_url) !!}
                        <span>اسکن برای دانلود</span>
                    </div>

                @else

                    <div class="software-qr">
                        <span>نرم‌افزار غیرفعال است</span>
                    </div>

                @endif


                {{-- فقط Super Admin مدیریت می‌کند --}}
                @if(auth()->user()->role->value === 'super_admin')

                    <div class="software-actions">

                        <form method="POST"
                              action="{{ route('admin.software.toggle', $item) }}">
                            @csrf
                            @method('PATCH')

                            <button type="submit" class="software-toggle">
                                {{ $item->is_active ? 'غیرفعال کردن' : 'فعال کردن' }}
                            </button>
                        </form>

                        <form method="POST"
                              action="{{ route('admin.software.destroy', $item) }}"
                              onsubmit="return confirm('این نرم‌افزار حذف شود؟');">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="software-delete">
                                حذف
                            </button>
                        </form>

                    </div>

                @endif

            </div>

        @empty

            <div class="software-empty">
                هنوز نرم‌افزاری ثبت نشده است.
            </div>

        @endforelse

    </div>
    ```

@endsection
