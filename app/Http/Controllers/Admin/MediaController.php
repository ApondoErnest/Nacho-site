<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MediaUpdateRequest;
use App\Http\Requests\Admin\MediaUploadRequest;
use App\Models\Media;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $type = trim((string) $request->query('type'));

        $media = Media::query()
            ->with('uploader')
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                $query->where('file_name', 'like', "%{$search}%")
                    ->orWhere('file_path', 'like', "%{$search}%")
                    ->orWhere('mime_type', 'like', "%{$search}%")
                    ->orWhere('alt_text_en', 'like', "%{$search}%")
                    ->orWhere('alt_text_fr', 'like', "%{$search}%");
            }))
            ->when($type !== '', fn ($query) => $query->where('file_type', $type))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.media.index', [
            'mediaItems' => $media,
            'search' => $search,
            'type' => $type,
            'types' => $this->types(),
            'counts' => $this->counts(),
        ]);
    }

    public function create(): View
    {
        return view('admin.media.create');
    }

    public function store(MediaUploadRequest $request): RedirectResponse
    {
        /** @var UploadedFile $file */
        $file = $request->file('file');
        $path = $file->storeAs(
            'media/'.now()->format('Y/m'),
            Str::uuid()->toString().'.'.$this->safeExtension($file),
            'public',
        );

        $media = Media::query()->create([
            'uploaded_by' => $request->user()?->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $this->fileType($file),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'alt_text_en' => $request->validated('alt_text_en'),
            'alt_text_fr' => $request->validated('alt_text_fr'),
        ]);

        return redirect()
            ->route('admin.media.show', $media)
            ->with('status', 'Media uploaded.');
    }

    public function show(Media $medium): View
    {
        $medium->load('uploader');

        return view('admin.media.show', [
            'media' => $medium,
        ]);
    }

    public function edit(Media $medium): View
    {
        return view('admin.media.edit', [
            'media' => $medium,
        ]);
    }

    public function update(MediaUpdateRequest $request, Media $medium): RedirectResponse
    {
        $medium->update($request->mediaAttributes());

        return redirect()
            ->route('admin.media.show', $medium)
            ->with('status', 'Media details updated.');
    }

    public function destroy(Media $medium): RedirectResponse
    {
        Storage::disk('public')->delete($medium->file_path);
        $medium->delete();

        return redirect()
            ->route('admin.media.index')
            ->with('status', 'Media deleted.');
    }

    /**
     * @return array<string, string>
     */
    private function types(): array
    {
        return [
            Media::TYPE_IMAGE => 'Images',
            Media::TYPE_DOCUMENT => 'Documents',
        ];
    }

    /**
     * @return array<string, int>
     */
    private function counts(): array
    {
        return [
            'total' => Media::query()->count(),
            'image' => Media::query()->where('file_type', Media::TYPE_IMAGE)->count(),
            'document' => Media::query()->where('file_type', Media::TYPE_DOCUMENT)->count(),
        ];
    }

    private function safeExtension(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        return in_array($extension, Media::ALLOWED_EXTENSIONS, true)
            ? $extension
            : ($file->guessExtension() ?: 'bin');
    }

    private function fileType(UploadedFile $file): string
    {
        if (str_starts_with((string) $file->getMimeType(), 'image/')) {
            return Media::TYPE_IMAGE;
        }

        return Media::TYPE_DOCUMENT;
    }
}
