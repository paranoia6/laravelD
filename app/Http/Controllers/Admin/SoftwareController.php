<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSoftwareRequest;
use App\Models\Software;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SoftwareController extends Controller
{
    public function index(): View
    {
        $software = Software::query()
            ->orderByDesc('id')
            ->get();

        return view('admin.software.index', compact('software'));
    }

    public function store(StoreSoftwareRequest $request): RedirectResponse
    {
        Software::create([
            'name' => $request->validated('name'),
            'download_url' => $request->validated('download_url'),
        ]);

        return back()->with('success', 'نرم‌افزار با موفقیت اضافه شد.');
    }

    public function toggle(Software $software): RedirectResponse
    {
        $software->update([
            'is_active' => ! $software->is_active,
        ]);

        return back()->with('success', 'وضعیت نرم‌افزار تغییر کرد.');
    }

    public function destroy(Software $software): RedirectResponse
    {
        $software->delete();

        return back()->with('success', 'نرم‌افزار حذف شد.');
    }
}
