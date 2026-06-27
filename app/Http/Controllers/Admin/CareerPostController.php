<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CareerPostStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CareerPostRequest;
use App\Models\CareerDepartment;
use App\Models\CareerPost;
use App\Models\Center;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CareerPostController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $status = trim((string) $request->query('status'));
        $departmentId = trim((string) $request->query('department_id'));
        $centerId = trim((string) $request->query('center_id'));

        $posts = CareerPost::query()
            ->with(['department', 'center', 'creator'])
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                $query->where('reference', 'like', "%{$search}%")
                    ->orWhere('title_en', 'like', "%{$search}%")
                    ->orWhere('title_fr', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('summary_en', 'like', "%{$search}%")
                    ->orWhere('summary_fr', 'like', "%{$search}%");
            }))
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($departmentId !== '', fn ($query) => $query->where('department_id', $departmentId))
            ->when($centerId !== '', fn ($query) => $query->where('center_id', $centerId))
            ->orderBy('display_order')
            ->latest('published_at')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.career-posts.index', [
            'posts' => $posts,
            'search' => $search,
            'status' => $status,
            'departmentId' => $departmentId,
            'centerId' => $centerId,
            'statuses' => CareerPostStatus::cases(),
            'departments' => $this->departments(),
            'centers' => $this->centers(),
            'counts' => $this->counts(),
        ]);
    }

    public function create(): View
    {
        return view('admin.career-posts.create', [
            'post' => new CareerPost([
                'status' => CareerPostStatus::DRAFT,
                'employment_type' => 'full-time',
                'vacancies_count' => 1,
                'allow_email_application' => true,
                'display_order' => 0,
            ]),
            'statuses' => CareerPostStatus::cases(),
            'departments' => $this->departments(),
            'centers' => $this->centers(),
            'employmentTypes' => CareerPost::employmentTypeOptions(),
        ]);
    }

    public function store(CareerPostRequest $request): RedirectResponse
    {
        $post = CareerPost::query()->create([
            ...$this->publicationAttributes($request->postAttributes()),
            'created_by' => $request->user()?->id,
        ]);

        return redirect()
            ->route('admin.career-posts.show', $post)
            ->with('status', 'Career vacancy created.');
    }

    public function show(CareerPost $careerPost): View
    {
        $careerPost->load(['department', 'center', 'creator']);

        return view('admin.career-posts.show', [
            'post' => $careerPost,
            'mailtoPreview' => $this->mailtoPreview($careerPost),
        ]);
    }

    public function edit(CareerPost $careerPost): View
    {
        return view('admin.career-posts.edit', [
            'post' => $careerPost,
            'statuses' => CareerPostStatus::cases(),
            'departments' => $this->departments(),
            'centers' => $this->centers(),
            'employmentTypes' => CareerPost::employmentTypeOptions(),
        ]);
    }

    public function update(CareerPostRequest $request, CareerPost $careerPost): RedirectResponse
    {
        $careerPost->update($this->publicationAttributes($request->postAttributes()));

        return redirect()
            ->route('admin.career-posts.show', $careerPost)
            ->with('status', 'Career vacancy updated.');
    }

    public function destroy(CareerPost $careerPost): RedirectResponse
    {
        $careerPost->update([
            'status' => CareerPostStatus::ARCHIVED,
            'allow_email_application' => false,
        ]);

        return redirect()
            ->route('admin.career-posts.index')
            ->with('status', 'Career vacancy archived.');
    }

    private function departments()
    {
        return CareerDepartment::query()
            ->orderBy('display_order')
            ->orderBy('name_en')
            ->get();
    }

    private function centers()
    {
        return Center::query()
            ->orderBy('display_order')
            ->orderBy('name_en')
            ->get();
    }

    /**
     * @return array<string, int>
     */
    private function counts(): array
    {
        return [
            'total' => CareerPost::query()->count(),
            'draft' => CareerPost::query()->where('status', CareerPostStatus::DRAFT->value)->count(),
            'published' => CareerPost::query()->where('status', CareerPostStatus::PUBLISHED->value)->count(),
            'closing_soon' => CareerPost::query()->where('status', CareerPostStatus::CLOSING_SOON->value)->count(),
            'closed' => CareerPost::query()->where('status', CareerPostStatus::CLOSED->value)->count(),
            'filled' => CareerPost::query()->where('status', CareerPostStatus::FILLED->value)->count(),
            'archived' => CareerPost::query()->where('status', CareerPostStatus::ARCHIVED->value)->count(),
        ];
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    private function publicationAttributes(array $attributes): array
    {
        if (in_array($attributes['status'] ?? null, [CareerPostStatus::PUBLISHED->value, CareerPostStatus::CLOSING_SOON->value], true) && blank($attributes['published_at'] ?? null)) {
            $attributes['published_at'] = now();
        }

        if (($attributes['status'] ?? null) === CareerPostStatus::DRAFT->value && blank($attributes['published_at'] ?? null)) {
            $attributes['published_at'] = null;
        }

        return $attributes;
    }

    private function mailtoPreview(CareerPost $post): ?string
    {
        if (! $post->application_email || ! $post->allow_email_application) {
            return null;
        }

        $title = $post->title_en;
        $reference = $post->reference;
        $subject = $post->application_subject
            ? strtr($post->application_subject, ['{title}' => $title, '{reference}' => $reference])
            : "Application - {$title} - {$reference}";
        $body = $post->application_instructions_en ?: "Hello NACHO team,\n\nI would like to apply for {$title} ({$reference}).";

        return 'mailto:'.$post->application_email.'?'.http_build_query([
            'subject' => $subject,
            'body' => $body,
        ], '', '&', PHP_QUERY_RFC3986);
    }
}
