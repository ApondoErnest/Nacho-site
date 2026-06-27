<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CareerDepartmentRequest;
use App\Models\CareerDepartment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CareerDepartmentController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $status = trim((string) $request->query('status'));

        $departments = CareerDepartment::query()
            ->withCount('posts')
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                $query->where('name_en', 'like', "%{$search}%")
                    ->orWhere('name_fr', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('description_en', 'like', "%{$search}%")
                    ->orWhere('description_fr', 'like', "%{$search}%");
            }))
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->orderBy('display_order')
            ->orderBy('name_en')
            ->paginate(12)
            ->withQueryString();

        return view('admin.career-departments.index', [
            'departments' => $departments,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        return view('admin.career-departments.create', [
            'department' => new CareerDepartment([
                'icon' => CareerDepartment::DEFAULT_ICON,
                'display_order' => 0,
                'is_active' => true,
            ]),
            'icons' => CareerDepartment::iconOptions(),
        ]);
    }

    public function store(CareerDepartmentRequest $request): RedirectResponse
    {
        $department = CareerDepartment::query()->create($request->departmentAttributes());

        return redirect()
            ->route('admin.career-departments.show', $department)
            ->with('status', 'Career department created.');
    }

    public function show(CareerDepartment $careerDepartment): View
    {
        $careerDepartment->loadCount('posts');
        $careerDepartment->load([
            'posts' => fn ($query) => $query->with('center')->orderBy('display_order')->latest('published_at')->latest(),
        ]);

        return view('admin.career-departments.show', [
            'department' => $careerDepartment,
        ]);
    }

    public function edit(CareerDepartment $careerDepartment): View
    {
        return view('admin.career-departments.edit', [
            'department' => $careerDepartment,
            'icons' => CareerDepartment::iconOptions(),
        ]);
    }

    public function update(CareerDepartmentRequest $request, CareerDepartment $careerDepartment): RedirectResponse
    {
        $careerDepartment->update($request->departmentAttributes());

        return redirect()
            ->route('admin.career-departments.show', $careerDepartment)
            ->with('status', 'Career department updated.');
    }

    public function destroy(CareerDepartment $careerDepartment): RedirectResponse
    {
        $careerDepartment->update(['is_active' => false]);

        return redirect()
            ->route('admin.career-departments.index')
            ->with('status', 'Career department deactivated.');
    }
}
