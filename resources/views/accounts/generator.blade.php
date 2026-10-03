<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ساخت اکانت</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px 15px;
            background: #f5f5f5;
            font-family: Tahoma, Arial, sans-serif;
        }

        .box {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
        }

        h2 {
            margin-top: 0;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
        }

        input,
        select {
            width: 100%;
            padding: 11px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 7px;
            background: white;
        }

        button {
            width: 100%;
            padding: 12px;
            border: 0;
            border-radius: 7px;
            background: #222;
            color: white;
            cursor: pointer;
        }

        .success {
            background: #e8f7ed;
            color: #176b35;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 7px;
        }

        .error {
            background: #fdeaea;
            color: #a52222;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 7px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background: #f5f5f5;
        }

        .result {
            margin-top: 25px;
        }

        .copy-btn {
            width: auto;
            padding: 5px 10px;
            font-size: 12px;
        }
    </style>
</head>

<body>

<div class="box">

    <h2>ساخت اکانت</h2>

    @if($errors->any())
        <div class="error">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @if(isset($createdAccounts))

        <div class="success">
            {{ count($createdAccounts) }} اکانت با موفقیت ساخته شد.
        </div>

        <div class="result">

                @foreach($createdAccounts as $index => $account)

                            {{ $account['username'] }}
                    <br>
                            {{ $account['password'] }}
                    <br>
                    <br>
                @endforeach

        </div>

    @else

        <form method="POST" action="{{ route('accounts.generator.store') }}">

            @csrf

            <label>Supporter ID</label>
            <input
                type="number"
                name="supporter_id"
                value="{{ old('supporter_id', 5) }}"
            >

            <label>پیشوند نام کاربری</label>
            <input
                type="text"
                name="username_prefix"
                value="{{ old('username_prefix', 'PilDoPing') }}"
                required
            >

            <label>تعداد اکانت</label>
            <input
                type="number"
                name="count"
                value="{{ old('count', 15) }}"
                min="1"
                max="1000"
                required
            >

            <label>نوع اکانت</label>
            <select name="account_type" required>
                <option value="1" {{ old('account_type', 1) == 1 ? 'selected' : '' }}>
                    عادی
                </option>

                <option value="2" {{ old('account_type') == 2 ? 'selected' : '' }}>
                    ویژه
                </option>
            </select>

            <button type="submit">
                ساخت اکانت‌ها
            </button>

        </form>

    @endif

</div>

</body>
</html>
