<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceRequest;
use App\Models\Service;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $status = trim((string) $request->query('status'));

        $services = Service::query()
            ->withCount(['centers', 'bookings'])
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                $query->where('title_en', 'like', "%{$search}%")
                    ->orWhere('title_fr', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('short_description_en', 'like', "%{$search}%")
                    ->orWhere('short_description_fr', 'like', "%{$search}%");
            }))
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->orderBy('display_order')
            ->orderBy('title_en')
            ->paginate(12)
            ->withQueryString();

        return view('admin.services.index', [
            'services' => $services,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        return view('admin.services.create', [
            'service' => new Service([
                'icon' => Service::DEFAULT_ICON,
                'is_active' => true,
                'display_order' => 0,
            ]),
            'icons' => Service::iconOptions(),
        ]);
    }

    public function store(ServiceRequest $request): RedirectResponse
    {
        $service = Service::query()->create($request->serviceAttributes());

        return redirect()
            ->route('admin.services.show', $service)
            ->with('status', 'Service created.');
    }

    public function show(Service $service): View
    {
        $service->load([
            'centers' => fn ($query) => $query->orderBy('display_order')->orderBy('name_en'),
        ])->loadCount('bookings');

        return view('admin.services.show', [
            'service' => $service,
        ]);
    }

    public function edit(Service $service): View
    {
        return view('admin.services.edit', [
            'service' => $service,
            'icons' => Service::iconOptions(),
        ]);
    }

    public function update(ServiceRequest $request, Service $service): RedirectResponse
    {
        $service->update($request->serviceAttributes());

        return redirect()
            ->route('admin.services.show', $service)
            ->with('status', 'Service updated.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $service->delete();

        return redirect()
            ->route('admin.services.index')
            ->with('status', 'Service archived.');
    }
}
