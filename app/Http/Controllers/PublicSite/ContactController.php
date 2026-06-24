<?php

namespace App\Http\Controllers\PublicSite;

use App\Enums\ContactMessageStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;
use App\Support\PublicSiteData;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(PublicSiteData $data): View
    {
        return view('public.contact', [
            'centerRecords' => $data->centers(),
            'headquarters' => $data->headquarters(),
        ]);
    }

    public function store(StoreContactMessageRequest $request): RedirectResponse
    {
        if (! $request->isSpam()) {
            ContactMessage::query()->create([
                'full_name' => $request->validated('full_name'),
                'email' => $request->validated('email'),
                'phone' => $request->validated('phone'),
                'subject' => $request->contactSubject(),
                'message' => $request->contactMessageBody(),
                'status' => ContactMessageStatus::NEW->value,
            ]);
        }

        return redirect(route('contact').'#contact-form')
            ->with('contact_message_sent', true);
    }
}
