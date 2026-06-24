<?php

namespace App\Http\Controllers\PublicSite;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Services\BookingReferenceService;
use App\Support\PublicSiteData;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function create(PublicSiteData $data): View
    {
        return view('public.book-inspection', [
            'centerRecords' => $data->centers(),
            'tariffPreviewRows' => $data->tariffPreview(),
            'serviceItems' => $data->services(),
        ]);
    }

    public function store(StoreBookingRequest $request, BookingReferenceService $referenceService): RedirectResponse
    {
        $validated = $request->validated();
        $booking = Booking::query()->create([
            'booking_reference' => $referenceService->generate($validated['preferred_date']),
            'full_name' => $validated['full_name'],
            'phone' => $request->normalizedPhone(),
            'email' => $validated['email'] ?? null,
            'center_id' => $request->bookingCenter()->getKey(),
            'service_id' => $request->bookingService()->getKey(),
            'tariff_id' => $request->bookingTariff()->getKey(),
            'vehicle_registration' => $validated['vehicle_registration'],
            'preferred_date' => $validated['preferred_date'],
            'preferred_time' => $validated['preferred_time'],
            'comment' => $request->bookingComment(),
            'consent' => true,
            'status' => BookingStatus::PENDING->value,
        ]);

        return redirect(route('book-inspection').'#book-inspection-form')
            ->with('booking_reference', $booking->booking_reference);
    }
}
