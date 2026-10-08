<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Support;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupportController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $supports = Support::query()
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

        return view(
            'admin.supports.show',
            compact('support')
        );
    }

    /**
     * ایجاد یک Support جدید
     */
    public function store(
        Request $request
    ): RedirectResponse {
        $user = $request->user();

        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:100',
                ],

                'links' => [
                    'nullable',
                    'array',
                ],

                'links.bale' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'links.eitaa' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'links.rubika' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'links.telegram' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'links.whatsapp' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'links.instagram' => [
                    'nullable',
                    'string',
                    'max:255',
                ],
            ],
            [
                'name.required' =>
                    'نام پشتیبانی الزامی است.',

                'name.max' =>
                    'نام پشتیبانی بیش از حد مجاز است.',
            ]
        );

        $links = $validated['links'] ?? [];

        $normalizedLinks = [];

        foreach (
            Support::networks()
            as $network => $label
        ) {
            $value = trim(
                (string) (
                    $links[$network] ?? ''
                )
            );

            if ($value === '') {
                $normalizedLinks[$network] = '';

                continue;
            }

            if (
                ! Support::validateNetworkValue(
                    $network,
                    $value
                )
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        "links.{$network}" =>
                            "مقدار واردشده برای {$label} معتبر نیست.",
                    ]);
            }

            $normalizedLinks[$network] =
                Support::buildLink(
                    $network,
                    $value
                );
        }

        $hasAtLeastOneLink = false;

        foreach ($normalizedLinks as $link) {
            if ($link !== '') {
                $hasAtLeastOneLink = true;

                break;
            }
        }

        if (! $hasAtLeastOneLink) {
            return back()
                ->withInput()
                ->withErrors([
                    'links' =>
                        'حداقل اطلاعات یک شبکه پشتیبانی را وارد کنید.',
                ]);
        }

        $support = Support::createWithLinks(
            (int) $user->id,
            $validated['name'],
            $normalizedLinks
        );

        return redirect()
            ->route('admin.supports')
            ->with(
                'success',
                "پشتیبانی «{$support->name}» با موفقیت ایجاد شد."
            );
    }

    /**
     * ویرایش Support
     */
    public function update(
        Request $request,
        Support $support
    ): RedirectResponse {
        if (
            (int) $support->user_id
            !== (int) $request->user()->id
        ) {
            abort(403);
        }

        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:100',
                ],

                'links' => [
                    'nullable',
                    'array',
                ],

                'links.bale' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'links.eitaa' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'links.rubika' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'links.telegram' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'links.whatsapp' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'links.instagram' => [
                    'nullable',
                    'string',
                    'max:255',
                ],
            ],
            [
                'name.required' =>
                    'نام پشتیبانی الزامی است.',

                'name.max' =>
                    'نام پشتیبانی بیش از حد مجاز است.',
            ]
        );

        $links = $validated['links'] ?? [];

        $normalizedLinks = [];

        foreach (
            Support::networks()
            as $network => $label
        ) {
            $value = trim(
                (string) (
                    $links[$network] ?? ''
                )
            );

            if ($value === '') {
                $normalizedLinks[$network] = '';

                continue;
            }

            if (
                ! Support::validateNetworkValue(
                    $network,
                    $value
                )
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        "links.{$network}" =>
                            "مقدار واردشده برای {$label} معتبر نیست.",
                    ]);
            }

            $normalizedLinks[$network] =
                Support::buildLink(
                    $network,
                    $value
                );
        }

        $hasAtLeastOneLink = false;

        foreach ($normalizedLinks as $link) {
            if ($link !== '') {
                $hasAtLeastOneLink = true;

                break;
            }
        }

        if (! $hasAtLeastOneLink) {
            return back()
                ->withInput()
                ->withErrors([
                    'links' =>
                        'حداقل اطلاعات یک شبکه پشتیبانی را وارد کنید.',
                ]);
        }

        $support->name =
            trim($validated['name']);

        $support->setLinks(
            $normalizedLinks
        );

        $support->save();

        return redirect()
            ->route('admin.supports')
            ->with(
                'success',
                "پشتیبانی «{$support->name}» با موفقیت ویرایش شد."
            );
    }

    /**
     * فعال / غیرفعال کردن Support
     */
    public function toggle(
        Request $request,
        Support $support
    ): RedirectResponse {
        if (
            (int) $support->user_id
            !== (int) $request->user()->id
        ) {
            abort(403);
        }

        $support->update([
            'is_active' =>
                ! $support->is_active,
        ]);

        return back()->with(
            'success',
            $support->is_active
                ? 'پشتیبانی فعال شد.'
                : 'پشتیبانی غیرفعال شد.'
        );
    }

    /**
     * حذف Support
     */
    public function destroy(
        Request $request,
        Support $support
    ): RedirectResponse {
        if (
            (int) $support->user_id
            !== (int) $request->user()->id
        ) {
            abort(403);
        }

        $support->delete();

        return redirect()
            ->route('admin.supports')
            ->with(
                'success',
                'پشتیبانی با موفقیت حذف شد.'
            );
    }
}
