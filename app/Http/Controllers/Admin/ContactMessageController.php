<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ContactMessageStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContactMessageStatusRequest;
use App\Http\Requests\Admin\ContactMessageUpdateRequest;
use App\Models\ContactMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $status = trim((string) $request->query('status'));
        $dateFrom = trim((string) $request->query('date_from'));
        $dateTo = trim((string) $request->query('date_to'));

        $messages = ContactMessage::query()
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                $query->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            }))
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($dateFrom !== '', fn ($query) => $query->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo !== '', fn ($query) => $query->whereDate('created_at', '<=', $dateTo))
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        return view('admin.contact-messages.index', [
            'messages' => $messages,
            'search' => $search,
            'status' => $status,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'statuses' => ContactMessageStatus::cases(),
            'counts' => $this->counts(),
        ]);
    }

    public function show(ContactMessage $contactMessage): View
    {
        return view('admin.contact-messages.show', [
            'message' => $contactMessage,
            'statuses' => ContactMessageStatus::cases(),
        ]);
    }

    public function update(ContactMessageUpdateRequest $request, ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->update($request->messageAttributes());

        return redirect()
            ->route('admin.contact-messages.show', $contactMessage)
            ->with('status', 'Contact message updated.');
    }

    public function updateStatus(ContactMessageStatusRequest $request, ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->update([
            'status' => $request->validated('status'),
        ]);

        return redirect()
            ->route('admin.contact-messages.show', $contactMessage)
            ->with('status', 'Contact message status updated.');
    }

    /**
     * @return array<string, int>
     */
    private function counts(): array
    {
        return [
            'total' => ContactMessage::query()->count(),
            'new' => ContactMessage::query()->status(ContactMessageStatus::NEW)->count(),
            'read' => ContactMessage::query()->status(ContactMessageStatus::READ)->count(),
            'replied' => ContactMessage::query()->status(ContactMessageStatus::REPLIED)->count(),
            'archived' => ContactMessage::query()->status(ContactMessageStatus::ARCHIVED)->count(),
        ];
    }
}
