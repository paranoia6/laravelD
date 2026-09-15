@extends('layouts.admin')

@section('title', 'مدیریت قیمت‌ها')

@section('content')

    <div class="container-fluid py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">مدیریت قیمت پلن‌ها</h4>
                <p class="text-muted mb-0">
                    قیمت پلن‌های عادی و ویژه را مدیریت کنید.
                </p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-4">

            @foreach($plans as $plan)

                @php
                    $typeLabel = $plan->type === 'normal'
                        ? 'عادی'
                        : 'ویژه';

                    $durationLabel = match((int) $plan->duration_months) {
                        1 => '۱ ماهه',
                        2 => '۲ ماهه',
                        3 => '۳ ماهه',
                        default => $plan->duration_months . ' ماهه',
                    };
                @endphp

                <div class="col-md-6 col-xl-4">

                    <div class="card h-100 shadow-sm">

                        <div class="card-header d-flex justify-content-between align-items-center">

                            <strong>
                                {{ $typeLabel }} - {{ $durationLabel }}
                            </strong>

                            @if($plan->is_active)
                                <span class="badge bg-success">
                                فعال
                            </span>
                            @else
                                <span class="badge bg-secondary">
                                غیرفعال
                            </span>
                            @endif

                        </div>

                        <div class="card-body">

                            <form
                                method="POST"
                                action="{{ route('admin.plans.update', $plan) }}"
                            >

                                @csrf
                                @method('PATCH')

                                <div class="mb-3">

                                    <label class="form-label">
                                        قیمت (تومان)
                                    </label>

                                    <input
                                        type="number"
                                        name="price"
                                        class="form-control"
                                        min="0"
                                        value="{{ old('price', $plan->price) }}"
                                        placeholder="مثلاً 500000"
                                    >

                                </div>

                                <div class="form-check mb-4">

                                    <input
                                        type="checkbox"
                                        class="form-check-input"
                                        name="is_active"
                                        value="1"
                                        id="active_{{ $plan->id }}"
                                        {{ $plan->is_active ? 'checked' : '' }}
                                    >

                                    <label
                                        class="form-check-label"
                                        for="active_{{ $plan->id }}"
                                    >
                                        پلن فعال باشد
                                    </label>

                                </div>

                                <button
                                    type="submit"
                                    class="btn btn-primary w-100"
                                >
                                    ذخیره تغییرات
                                </button>

                            </form>

                        </div>

                    </div>

                </div>

            @endforeach

        </div>

    </div>

    <style>

        /* =========================================================
           PLANS - COMPACT 3 x 2
           ========================================================= */

        .plans-page-fix {
            width: 100%;
        }

        @media (min-width: 1200px) {

            .container-fluid.py-4 > .row.g-4 {
                display: grid !important;
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
                gap: 16px !important;
            }

            .container-fluid.py-4 > .row.g-4 > [class*="col-"] {
                width: auto !important;
                max-width: none !important;
                flex: none !important;
                padding: 0 !important;
            }

        }

        .container-fluid.py-4 > .row.g-4 .card {
            min-height: 0 !important;
            height: auto !important;

            border: 1px solid #eadde4 !important;
            border-radius: 15px !important;

            box-shadow:
                0 4px 15px rgba(100, 50, 75, .045) !important;

            overflow: hidden;
        }

        .container-fluid.py-4 > .row.g-4 .card-header {
            min-height: 48px;
            padding: 12px 15px !important;

            background: #fffafd !important;
            border-bottom: 1px solid #f0e5ea !important;

            font-size: 12px;
        }

        .container-fluid.py-4 > .row.g-4 .card-body {
            padding: 15px !important;
        }

        .container-fluid.py-4 > .row.g-4 .form-label {
            margin-bottom: 6px;
            color: #71717a;
            font-size: 11px;
            font-weight: 800;
        }

        .container-fluid.py-4 > .row.g-4 .form-control {
            height: 40px;
            min-height: 40px;
            border-radius: 9px;
            font-size: 12px;
        }

        .container-fluid.py-4 > .row.g-4 .form-check {
            margin-bottom: 14px !important;
        }

        .container-fluid.py-4 > .row.g-4 .form-check-label {
            font-size: 11px;
        }

        .container-fluid.py-4 > .row.g-4 button {
            height: 39px;
            border: 0 !important;
            border-radius: 9px;

            background: #c64d7c !important;
            border-color: #c64d7c !important;

            font-family: inherit;
            font-size: 11px;
            font-weight: 800;
        }

        .container-fluid.py-4 > .row.g-4 button:hover {
            background: #b84270 !important;
        }

        @media (max-width: 1199px) and (min-width: 768px) {

            .container-fluid.py-4 > .row.g-4 {
                row-gap: 16px !important;
            }

        }

    </style>

@endsection
