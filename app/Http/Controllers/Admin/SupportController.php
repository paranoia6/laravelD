<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Support;
use App\Models\SupportLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupportController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        Support::ensureDefaultsFor($user);

        $supports = Support::query()
            ->with([
                'links' => function ($query) {
                    $query->latest('id');
                },
            ])
            ->where('user_id', $user->id)
            ->orderBy('id')
            ->get();

        return view(
            'admin.supports.index',
            compact('supports')
        );
    }

    public function show(
        Request $request,
        Support $support
    ): View {
        if (
            (int) $support->user_id
            !== (int) $request->user()->id
        ) {
            abort(403);
        }

        $support->load([
            'links' => function ($query) {
                $query->latest('id');
            },
        ]);

        return view(
            'admin.supports.show',
            compact('support')
        );
    }

    /**
     * ثبت یک راه ارتباطی برای یک شبکه
     */
    public function store(
        Request $request,
        Support $support
    ): RedirectResponse {

        if (
            (int) $support->user_id
            !== (int) $request->user()->id
        ) {
            abort(403);
        }

        if (! $support->is_active) {
            return back()->withErrors([
                'support' =>
                    'این شبکه پشتیبانی غیرفعال است.',
            ]);
        }

        /*
         * برای هر شبکه فقط یک راه ارتباطی.
         */
        if ($support->links()->exists()) {
            return back()->withErrors([
                'support' =>
                    'برای این شبکه قبلاً اطلاعات ثبت شده است.',
            ]);
        }

        $network =
            $support->network_key;

        $rules = [
            'value' => [
                'required',
                'string',
                'max:255',
            ],
        ];

        $messages = [
            'value.required' =>
                'اطلاعات پشتیبانی الزامی است.',

            'value.max' =>
                'اطلاعات پشتیبانی بیش از حد مجاز است.',
        ];

        /*
         * واتساپ = شماره
         */
        if ($network === 'whatsapp') {

            $rules['value'][] =
                'regex:/^[0-9]{8,15}$/';

            $messages['value.regex'] =
                'شماره واتساپ را با کد کشور، بدون + و فاصله وارد کنید. مثال: 989121234567';

        } else {

            /*
             * سایر پیام‌رسان‌ها = ID
             */
            $rules['value'][] =
                'regex:/^[A-Za-z0-9_.-]+$/';

            $messages['value.regex'] =
                'آیدی فقط می‌تواند شامل حروف انگلیسی، اعداد، نقطه، خط تیره و زیرخط باشد.';
        }

        $validated =
            $request->validate(
                $rules,
                $messages
            );

        $value =
            trim($validated['value']);

        $link =
            $this->buildLink(
                $network,
                $value
            );

        $title =
            $support->network_label;

        SupportLink::create([
            'support_id' =>
                $support->id,

            'title' =>
                $title,

            'link' =>
                $link,

            'is_active' =>
                true,
        ]);

        return redirect()
            ->route('admin.supports')
            ->with(
                'success',
                "اطلاعات {$title} با موفقیت ثبت شد."
            );
    }

    /**
     * فعال / غیرفعال کردن SupportLink
     */
    public function toggle(
        Request $request,
        SupportLink $supportLink
    ): RedirectResponse {

        $supportLink->load('support');

        if (
            ! $supportLink->support
            ||
            (int) $supportLink->support->user_id
            !== (int) $request->user()->id
        ) {
            abort(403);
        }

        $supportLink->update([
            'is_active' =>
                ! $supportLink->is_active,
        ]);

        return back()->with(
            'success',
            $supportLink->is_active
                ? 'پشتیبانی فعال شد.'
                : 'پشتیبانی غیرفعال شد.'
        );
    }

    /**
     * حذف SupportLink
     */
    public function destroy(
        Request $request,
        SupportLink $supportLink
    ): RedirectResponse {

        $supportLink->load('support');

        if (
            ! $supportLink->support
            ||
            (int) $supportLink->support->user_id
            !== (int) $request->user()->id
        ) {
            abort(403);
        }

        $supportLink->delete();

        return back()->with(
            'success',
            'اطلاعات پشتیبانی حذف شد.'
        );
    }

    /**
     * تبدیل مقدار واردشده به URL
     */
    private function buildLink(
        string $network,
        string $value
    ): string {
        return match ($network) {

            'whatsapp' =>
                'https://wa.me/' . $value,

            'instagram' =>
                'https://instagram.com/'
                . ltrim($value, '@'),

            'telegram' =>
                'https://t.me/'
                . ltrim($value, '@'),

            'rubika' =>
                'https://rubika.ir/'
                . ltrim($value, '@'),

            'eitaa' =>
                'https://eitaa.com/'
                . ltrim($value, '@'),

            'bale' =>
                'https://ble.ir/'
                . ltrim($value, '@'),

            default =>
            $value,
        };
    }
}
