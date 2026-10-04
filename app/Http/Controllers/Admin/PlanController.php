<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(): View
    {
        $plans = Plan::query()
            ->orderByRaw("CASE WHEN type = 'normal' THEN 1 ELSE 2 END")
            ->orderBy('duration_months')
            ->get();

        return view('admin.plans.index', compact('plans'));
    }

    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'price' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $plan->update([
            'price' => $validated['price'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.plans')
            ->with('success', 'قیمت پلن با موفقیت بروزرسانی شد.');
    }
}
