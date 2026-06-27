<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BookingStatusRequest;
use App\Http\Requests\Admin\BookingUpdateRequest;
use App\Models\Booking;
use App\Models\Center;
use App\Models\Service;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $status = trim((string) $request->query('status'));
        $centerId = trim((string) $request->query('center_id'));
        $serviceId = trim((string) $request->query('service_id'));
        $dateFrom = trim((string) $request->query('date_from'));
        $dateTo = trim((string) $request->query('date_to'));

        $bookings = Booking::query()
            ->with(['center', 'service', 'tariff'])
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                $query->where('booking_reference', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('vehicle_registration', 'like', "%{$search}%");
            }))
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($centerId !== '', fn ($query) => $query->where('center_id', $centerId))
            ->when($serviceId !== '', fn ($query) => $query->where('service_id', $serviceId))
            ->when($dateFrom !== '', fn ($query) => $query->whereDate('preferred_date', '>=', $dateFrom))
            ->when($dateTo !== '', fn ($query) => $query->whereDate('preferred_date', '<=', $dateTo))
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        return view('admin.bookings.index', [
            'bookings' => $bookings,
            'search' => $search,
            'status' => $status,
            'centerId' => $centerId,
            'serviceId' => $serviceId,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'statuses' => BookingStatus::cases(),
            'centers' => $this->centers(),
            'services' => $this->services(),
        ]);
    }

    public function show(Booking $booking): View
    {
        $booking->load(['center', 'service', 'tariff']);

        return view('admin.bookings.show', [
            'booking' => $booking,
            'statuses' => BookingStatus::cases(),
        ]);
    }

    public function update(BookingUpdateRequest $request, Booking $booking): RedirectResponse
    {
        $booking->update($request->bookingAttributes());

        return redirect()
            ->route('admin.bookings.show', $booking)
            ->with('status', 'Booking updated.');
    }

    public function updateStatus(BookingStatusRequest $request, Booking $booking): RedirectResponse
    {
        $booking->update([
            'status' => $request->validated('status'),
        ]);

        return redirect()
            ->route('admin.bookings.show', $booking)
            ->with('status', 'Booking status updated.');
    }

    private function centers()
    {
        return Center::query()
            ->orderBy('display_order')
            ->orderBy('name_en')
            ->get();
    }

    private function services()
    {
        return Service::query()
            ->orderBy('display_order')
            ->orderBy('title_en')
            ->get();
    }
}
