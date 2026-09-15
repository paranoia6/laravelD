@extends('layouts.admin')
@section('content')

<div class="container-fluid py-4">

    <div class="mb-4">
        <h4>کیف پول</h4>
    </div>

    @if(auth()->user()->role->value === 'admin')

        <div class="card mb-4">

            <div class="card-body">

                <div class="text-muted">
                    موجودی فعلی
                </div>

                <h2 class="mt-2">
                    {{ number_format($balance) }}
                    <small>تومان</small>
                </h2>

                @if($balance < 100000)

                    <div class="alert alert-danger mt-3 mb-0">
                        موجودی شما کمتر از ۱۰۰٬۰۰۰ تومان است.
                    </div>

                @endif

            </div>

        </div>

    @else

        <div class="card mb-4">

            <div class="card-header">
                موجودی Adminها
            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-hover">

                        <thead>
                        <tr>
                            <th>Admin</th>
                            <th>موجودی</th>
                            <th>وضعیت</th>
                            <th></th>
                        </tr>
                        </thead>

                        <tbody>

                        @foreach($admins as $admin)

                            <tr>

                                <td>
                                    {{ $admin->email }}
                                </td>

                                <td>
                                    {{ number_format((int) $admin->balance) }}
                                    تومان
                                </td>

                                <td>

                                    @if($admin->is_active)
                                        <span class="badge bg-success">
                                            فعال
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            مسدود
                                        </span>
                                    @endif

                                </td>

                                <td>



                                </td>

                            </tr>

                        @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    @endif


    <div class="card">

        <div class="card-header">
            تاریخچه تراکنش‌ها
        </div>

        <div class="card-body">

            @if($transactions->count())

                <div class="table-responsive">

                    <table class="table table-hover">

                        <thead>
                        <tr>
                            <th>تاریخ</th>
                            @if(auth()->user()->role->value === 'super_admin')
                                <th>Admin</th>
                            @endif
                            <th>نوع</th>
                            <th>مبلغ</th>
                            <th>موجودی بعد</th>
                            <th>توضیح</th>
                            <th>ثبت توسط</th>
                        </tr>
                        </thead>

                        <tbody>

                        @foreach($transactions as $transaction)

                            <tr>

                                <td>
                                    {{ jalali_date($transaction->created_at) }}
                                </td>

                                @if(auth()->user()->role->value === 'super_admin')
                                    <td>
                                        {{ $transaction->user->email ?? '-' }}
                                    </td>
                                @endif

                                <td>

                                    @if($transaction->type === 'credit')
                                        <span class="badge bg-success">
                                            شارژ
                                        </span>
                                    @elseif($transaction->type === 'debit')
                                        <span class="badge bg-danger">
                                            برداشت
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">
                                            اصلاح
                                        </span>
                                    @endif

                                </td>

                                <td>
                                    {{ number_format((int) $transaction->amount) }}
                                    تومان
                                </td>

                                <td>
                                    {{ number_format((int) $transaction->balance_after) }}
                                    تومان
                                </td>

                                <td>
                                    {{ $transaction->description ?? '-' }}
                                </td>

                                <td>
                                    {{ $transaction->creator->email ?? '-' }}
                                </td>

                            </tr>

                        @endforeach

                        </tbody>

                    </table>

                </div>

                <div class="mt-3">
                    {{ $transactions->links() }}
                </div>

            @else

                <div class="text-center text-muted py-5">
                    تراکنشی ثبت نشده است.
                </div>

            @endif

        </div>

    </div>

</div>

@endsection
