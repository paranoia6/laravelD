<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreConfigRequest;
use App\Models\Config;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConfigController extends Controller
{
    public function index(): View
    {
        $configs = Config::query()
            ->latest('id')
            ->paginate(20);

        return view('admin.configs.index', compact('configs'));
    }

    public function show(Config $config): View
    {
        return view('admin.configs.show', compact('config'));
    }

    public function store(StoreConfigRequest $request): RedirectResponse
    {
        Config::create([
            'config' => $request->validated('config'),
            'internet_type' => $request->validated('internet_type'),
            'account_type' => $request->validated('account_type'),
            'descriptions' => $request->validated('descriptions'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'کانفیگ با موفقیت اضافه شد.');
    }

    public function update(StoreConfigRequest $request, Config $config): RedirectResponse
    {
        $config->update([
            'config' => $request->validated('config'),
            'internet_type' => $request->validated('internet_type'),
            'account_type' => $request->validated('account_type'),
            'descriptions' => $request->validated('descriptions'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.configs.show', $config)
            ->with('success', 'کانفیگ با موفقیت ویرایش شد.');
    }

    public function destroy(Config $config): RedirectResponse
    {
        $config->delete();

        return redirect()
            ->route('admin.configs')
            ->with('success', 'کانفیگ با موفقیت حذف شد.');
    }
}
