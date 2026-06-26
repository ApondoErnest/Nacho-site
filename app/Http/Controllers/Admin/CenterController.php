<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CenterStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CenterRequest;
use App\Models\Center;
use App\Models\Service;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CenterController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $status = trim((string) $request->query('status'));

        $centers = Center::query()
            ->withCount(['contacts', 'hours', 'services'])
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                $query->where('name_en', 'like', "%{$search}%")
                    ->orWhere('name_fr', 'like', "%{$search}%")
                    ->orWhere('city_en', 'like', "%{$search}%")
                    ->orWhere('city_fr', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            }))
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->orderBy('display_order')
            ->orderBy('name_en')
            ->paginate(12)
            ->withQueryString();

        return view('admin.centers.index', [
            'centers' => $centers,
            'search' => $search,
            'status' => $status,
            'statuses' => CenterStatus::cases(),
        ]);
    }

    public function create(): View
    {
        return view('admin.centers.create', [
            'center' => new Center([
                'status' => CenterStatus::ACTIVE,
                'display_order' => 0,
                'is_active' => true,
                'booking_enabled' => false,
            ]),
            'services' => $this->services(),
            'selectedServices' => collect(),
            'statuses' => CenterStatus::cases(),
        ]);
    }

    public function store(CenterRequest $request): RedirectResponse
    {
        $center = Center::query()->create($request->centerAttributes());
        $center->services()->sync($request->serviceSyncPayload());

        return redirect()
            ->route('admin.centers.show', $center)
            ->with('status', 'Center created.');
    }

    public function show(Center $center): View
    {
        $center->load([
            'contacts' => fn ($query) => $query->orderByDesc('is_primary')->orderBy('display_order'),
            'hours' => fn ($query) => $query->orderBy('day_of_week'),
            'services' => fn ($query) => $query->orderBy('display_order'),
        ]);

        return view('admin.centers.show', [
            'center' => $center,
        ]);
    }

    public function edit(Center $center): View
    {
        $center->load('services');

        return view('admin.centers.edit', [
            'center' => $center,
            'services' => $this->services(),
            'selectedServices' => $center->services->pluck('id'),
            'statuses' => CenterStatus::cases(),
        ]);
    }

    public function update(CenterRequest $request, Center $center): RedirectResponse
    {
        $center->update($request->centerAttributes());
        $center->services()->sync($request->serviceSyncPayload());

        return redirect()
            ->route('admin.centers.show', $center)
            ->with('status', 'Center updated.');
    }

    public function destroy(Center $center): RedirectResponse
    {
        $center->delete();

        return redirect()
            ->route('admin.centers.index')
            ->with('status', 'Center archived.');
    }

    private function services()
    {
        return Service::query()
            ->orderBy('display_order')
            ->orderBy('title_en')
            ->get();
    }
}
