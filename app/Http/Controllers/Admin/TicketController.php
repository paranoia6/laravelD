<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = Ticket::query()
            ->with([
                'admin',
                'latestMessage.user',
            ])
            ->withCount('messages')
            ->latest('id');

        if ($user->role->value === 'admin') {
            $query->where('admin_id', $user->id);
        }

        $tickets = $query
            ->paginate(20)
            ->withQueryString();

        return view('admin.tickets.index', compact('tickets'));
    }

    public function create(): View
    {
        return view('admin.tickets.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->role->value !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'subject' => [
                'required',
                'string',
                'max:255',
            ],
            'message' => [
                'required',
                'string',
                'max:5000',
            ],
            'priority' => [
                'required',
                'in:low,normal,high',
            ],
        ], [
            'subject.required' => 'موضوع تیکت الزامی است.',
            'subject.max' => 'موضوع تیکت بیش از حد مجاز است.',
            'message.required' => 'متن پیام الزامی است.',
            'message.max' => 'متن پیام بیش از حد مجاز است.',
            'priority.required' => 'اولویت را انتخاب کنید.',
            'priority.in' => 'اولویت انتخاب‌شده معتبر نیست.',
        ]);

        $ticket = Ticket::create([
            'admin_id' => $user->id,
            'subject' => $validated['subject'],
            'status' => Ticket::STATUS_PENDING,
            'priority' => $validated['priority'],
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $validated['message'],
        ]);

        User::query()
            ->where('role', 'super_admin')
            ->where('is_active', true)
            ->get()
            ->each(function (User $superAdmin) use ($ticket, $user) {
                $superAdmin->notify(new SystemNotification(
                    'تیکت جدید',
                    "Admin {$user->email} یک تیکت جدید با عنوان «{$ticket->subject}» ثبت کرد.",
                    'warning',
                    route('admin.tickets.show', $ticket)
                ));
            });

        return redirect()
            ->route('admin.tickets.show', $ticket)
            ->with('success', 'تیکت با موفقیت ثبت شد و در انتظار پاسخ Super Admin است.');
    }

    public function show(Request $request, Ticket $ticket): View
    {
        $this->authorizeTicket($request, $ticket);

        $ticket->load([
            'admin',
            'messages.user',
        ]);

        return view('admin.tickets.show', compact('ticket'));
    }

    public function reply(
        Request $request,
        Ticket $ticket
    ): RedirectResponse {
        $this->authorizeTicket($request, $ticket);

        if ($ticket->status === Ticket::STATUS_CLOSED) {
            return back()->withErrors([
                'message' => 'این تیکت بسته شده است.',
            ]);
        }

        $validated = $request->validate([
            'message' => [
                'required',
                'string',
                'max:5000',
            ],
        ], [
            'message.required' => 'متن پیام الزامی است.',
            'message.max' => 'متن پیام بیش از حد مجاز است.',
        ]);

        $user = $request->user();

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $validated['message'],
        ]);

        if ($user->role->value === 'admin') {
            $ticket->update([
                'status' => Ticket::STATUS_PENDING,
            ]);

            User::query()
                ->where('role', 'super_admin')
                ->where('is_active', true)
                ->get()
                ->each(function (User $superAdmin) use ($ticket, $user) {
                    $superAdmin->notify(new SystemNotification(
                        'پاسخ جدید در تیکت',
                        "Admin {$user->email} در تیکت «{$ticket->subject}» پیام جدید ارسال کرد.",
                        'warning',
                        route('admin.tickets.show', $ticket)
                    ));
                });
        } else {
            $ticket->update([
                'status' => Ticket::STATUS_OPEN,
            ]);

            if ($ticket->admin) {
                $ticket->admin->notify(new SystemNotification(
                    'پاسخ تیکت دریافت شد',
                    "Super Admin به تیکت «{$ticket->subject}» پاسخ داد.",
                    'success',
                    route('admin.tickets.show', $ticket)
                ));
            }
        }

        return back()->with(
            'success',
            $user->role->value === 'admin'
                ? 'پیام ثبت شد و تیکت در انتظار پاسخ Super Admin است.'
                : 'پاسخ Super Admin ثبت شد.'
        );
    }

    public function status(
        Request $request,
        Ticket $ticket
    ): RedirectResponse {
        $user = $request->user();

        if ($user->role->value !== 'super_admin') {
            abort(403);
        }

        $validated = $request->validate([
            'status' => [
                'required',
                'in:open,pending,closed',
            ],
        ]);

        $ticket->update([
            'status' => $validated['status'],
        ]);

        if ($ticket->admin) {
            $statusText = match ($validated['status']) {
                'open' => 'باز',
                'pending' => 'در انتظار پاسخ',
                'closed' => 'بسته',
            };

            $ticket->admin->notify(new SystemNotification(
                'وضعیت تیکت تغییر کرد',
                "وضعیت تیکت «{$ticket->subject}» به «{$statusText}» تغییر کرد.",
                'info',
                route('admin.tickets.show', $ticket)
            ));
        }

        return back()->with('success', 'وضعیت تیکت با موفقیت تغییر کرد.');
    }

    private function authorizeTicket(
        Request $request,
        Ticket $ticket
    ): void {
        $user = $request->user();

        if (
            $user->role->value === 'admin'
            && (int) $ticket->admin_id !== (int) $user->id
        ) {
            abort(403);
        }
    }
}
