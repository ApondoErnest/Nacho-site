<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TariffRevisionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TariffRequest;
use App\Http\Requests\Admin\TariffRevisionRequest;
use App\Models\Tariff;
use App\Models\TariffAuditLog;
use App\Models\TariffRevision;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class TariffController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $status = trim((string) $request->query('status'));
        $bookable = trim((string) $request->query('bookable'));

        $tariffs = Tariff::query()
            ->withCount(['bookings', 'revisions', 'auditLogs'])
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                $query->where('category_code', 'like', "%{$search}%")
                    ->orWhere('category_slug', 'like', "%{$search}%")
                    ->orWhere('name_en', 'like', "%{$search}%")
                    ->orWhere('name_fr', 'like', "%{$search}%");
            }))
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->when($bookable === 'yes', fn ($query) => $query->where('is_bookable', true))
            ->when($bookable === 'no', fn ($query) => $query->where('is_bookable', false))
            ->orderBy('display_order')
            ->orderBy('category_code')
            ->orderBy('name_en')
            ->paginate(12)
            ->withQueryString();

        return view('admin.tariffs.index', [
            'tariffs' => $tariffs,
            'search' => $search,
            'status' => $status,
            'bookable' => $bookable,
        ]);
    }

    public function create(): View
    {
        return view('admin.tariffs.create', [
            'tariff' => new Tariff([
                'vehicle_icon' => Tariff::DEFAULT_VEHICLE_ICON,
                'validity_unit' => 'months',
                'validity_value' => 12,
                'display_order' => 0,
                'is_active' => true,
                'is_bookable' => true,
            ]),
            'icons' => Tariff::vehicleIconOptions(),
            'validityUnits' => Tariff::validityUnitOptions(),
        ]);
    }

    public function store(TariffRequest $request): RedirectResponse
    {
        $tariff = Tariff::query()->create($request->tariffAttributes());

        $this->recordAudit($tariff, [
            'created' => [
                'old' => null,
                'new' => Arr::only($tariff->getRawOriginal(), Tariff::AUDITED_FIELDS),
            ],
        ]);

        return redirect()
            ->route('admin.tariffs.show', $tariff)
            ->with('status', 'Tariff created.');
    }

    public function show(Tariff $tariff): View
    {
        $tariff->loadCount('bookings');
        $tariff->load([
            'revisions' => fn ($query) => $query->with('creator')->latest('effective_date')->latest(),
            'auditLogs' => fn ($query) => $query->with('user')->latest(),
        ]);

        return view('admin.tariffs.show', [
            'tariff' => $tariff,
            'effectiveSnapshot' => $tariff->effectiveSnapshot(),
            'icons' => Tariff::vehicleIconOptions(),
            'validityUnits' => Tariff::validityUnitOptions(),
        ]);
    }

    public function edit(Tariff $tariff): View
    {
        return view('admin.tariffs.edit', [
            'tariff' => $tariff,
            'icons' => Tariff::vehicleIconOptions(),
            'validityUnits' => Tariff::validityUnitOptions(),
        ]);
    }

    public function update(TariffRequest $request, Tariff $tariff): RedirectResponse
    {
        $this->updateWithAudit($tariff, $request->tariffAttributes());

        return redirect()
            ->route('admin.tariffs.show', $tariff)
            ->with('status', 'Tariff updated.');
    }

    public function destroy(Tariff $tariff): RedirectResponse
    {
        $this->updateWithAudit($tariff, [
            'is_active' => false,
            'is_bookable' => false,
        ]);

        return redirect()
            ->route('admin.tariffs.index')
            ->with('status', 'Tariff deactivated.');
    }

    public function storeRevision(TariffRevisionRequest $request, Tariff $tariff): RedirectResponse
    {
        $revision = TariffRevision::query()->create([
            'tariff_id' => $tariff->id,
            'created_by' => $request->user()?->id,
            'snapshot' => $request->snapshotAttributes(),
            'effective_date' => $request->validated('effective_date'),
            'status' => TariffRevisionStatus::SCHEDULED,
        ]);

        $this->recordAudit($tariff, [
            'scheduled_revision' => [
                'old' => null,
                'new' => [
                    'effective_date' => $revision->effective_date?->toDateString(),
                    'snapshot' => $revision->snapshot,
                ],
            ],
        ]);

        return redirect()
            ->route('admin.tariffs.show', $tariff)
            ->with('status', 'Tariff revision scheduled.');
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function updateWithAudit(Tariff $tariff, array $attributes): void
    {
        $before = Arr::only($tariff->getRawOriginal(), Tariff::AUDITED_FIELDS);

        $tariff->fill($attributes);
        $dirtyFields = array_values(array_intersect(array_keys($tariff->getDirty()), Tariff::AUDITED_FIELDS));

        if ($dirtyFields === []) {
            return;
        }

        $tariff->save();

        $changes = [];

        foreach ($dirtyFields as $field) {
            $changes[$field] = [
                'old' => $before[$field] ?? null,
                'new' => $tariff->getRawOriginal($field),
            ];
        }

        $this->recordAudit($tariff, $changes);
    }

    /**
     * @param  array<string, mixed>  $changes
     */
    private function recordAudit(Tariff $tariff, array $changes): void
    {
        if ($changes === []) {
            return;
        }

        TariffAuditLog::query()->create([
            'tariff_id' => $tariff->id,
            'user_id' => request()->user()?->id,
            'changes' => $changes,
        ]);
    }
}
