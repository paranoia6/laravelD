<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['normal', 'special'])],
            'duration_months' => ['required', 'integer', Rule::in([1, 2, 3])],
            'price' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $exists = Plan::query()
            ->where('type', $validated['type'])
            ->where('duration_months', $validated['duration_months'])
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['duration_months' => 'این نوع پلن با این مدت از قبل وجود دارد.']);
        }

        Plan::create([
            'type' => $validated['type'],
            'duration_months' => (int) $validated['duration_months'],
            'price' => $validated['price'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.plans')
            ->with('success', 'پلن با موفقیت ایجاد شد.');
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
            ->with('success', 'تغییرات پلن ذخیره شد.');
    }
}
