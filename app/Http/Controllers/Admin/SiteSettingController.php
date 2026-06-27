<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SiteSettingUpdateRequest;
use App\Models\SiteSetting;
use App\Support\SiteSettingRegistry;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SiteSettingController extends Controller
{
    public function index(): View
    {
        return view('admin.site-settings.index', [
            'groupedSettings' => SiteSettingRegistry::settingsByGroup(),
            'counts' => SiteSettingRegistry::counts(),
        ]);
    }

    public function update(SiteSettingUpdateRequest $request): RedirectResponse
    {
        foreach ($request->settingValues() as $key => $value) {
            $definition = SiteSettingRegistry::definitionFor($key);

            SiteSetting::query()->updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'type' => $definition['type']->value,
                ],
            );
        }

        return redirect()
            ->route('admin.site-settings.index')
            ->with('status', 'Site settings updated.');
    }
}
