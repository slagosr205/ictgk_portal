<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branding;
use App\Support\Branding\BrandingManager;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class BrandingAdminController extends Controller
{
    public function edit(Request $request, ?string $brandKey = null)
    {
        $brandKey = $brandKey ?: app(BrandingManager::class)->activeKey();

        $defaults = (array) config('branding.brands.'.$brandKey, []);
        if (empty($defaults)) {
            abort(404, 'Brand desconocido: '.$brandKey);
        }

        $branding = Branding::firstOrCreate(
            ['key' => $brandKey],
            [
                'name' => (string) Arr::get($defaults, 'name', $brandKey),
                'assets' => (array) Arr::get($defaults, 'assets', []),
                'palette' => (array) Arr::get($defaults, 'palette', []),
                'texts' => (array) Arr::get($defaults, 'texts', ['portal_title' => 'Portal de Reclutamiento']),
            ]
        );

        $catalog = array_keys((array) config('branding.brands', []));
        $envBrandKey = (string) config('branding.active', 'altia');
        $currentBrandKey = app(BrandingManager::class)->activeKey();

        return view('admin.branding.edit', [
            'brandingRow' => $branding,
            'brandKey' => $brandKey,
            'envBrandKey' => $envBrandKey,
            'currentBrandKey' => $currentBrandKey,
            'brandCatalog' => $catalog,
            'resolvedBranding' => app(BrandingManager::class)->publicFor($brandKey),
        ]);
    }

    public function update(Request $request, string $brandKey)
    {
        $defaults = (array) config('branding.brands.'.$brandKey, []);
        if (empty($defaults)) {
            abort(404, 'Brand desconocido: '.$brandKey);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'texts.portal_title' => ['nullable', 'string', 'max:255'],

            // palette
            'palette.primary' => ['required', 'string', 'max:32'],
            'palette.secondary' => ['required', 'string', 'max:32'],
            'palette.accent' => ['required', 'string', 'max:32'],
            'palette.light' => ['required', 'string', 'max:32'],
            'palette.dark' => ['required', 'string', 'max:32'],
            'palette.danger' => ['required', 'string', 'max:32'],
            'palette.success' => ['required', 'string', 'max:32'],
            'palette.warning' => ['required', 'string', 'max:32'],
            'palette.text_primary' => ['required', 'string', 'max:32'],
            'palette.text_secondary' => ['required', 'string', 'max:32'],
            'palette.border' => ['required', 'string', 'max:32'],

            // assets (uploads optional)
            'logo' => ['nullable', 'file', 'max:4096', 'mimes:svg,png,jpg,jpeg,webp'],
            'logo_light' => ['nullable', 'file', 'max:4096', 'mimes:svg,png,jpg,jpeg,webp'],
            'background' => ['nullable', 'file', 'max:8192', 'mimes:png,jpg,jpeg,webp'],
            'favicon' => ['nullable', 'file', 'max:2048', 'mimes:svg,png,ico'],
        ]);

        $row = Branding::firstOrCreate(
            ['key' => $brandKey],
            ['name' => $brandKey]
        );

        $assets = (array) ($row->assets ?? Arr::get($defaults, 'assets', []));

        // Store uploads directly under /public/branding/{brandKey} to avoid relying on storage:link.
        $publicDir = public_path('branding/'.trim($brandKey, '/'));
        if (! File::exists($publicDir)) {
            File::makeDirectory($publicDir, 0755, true);
        }

        foreach (['logo', 'logo_light', 'background', 'favicon'] as $field) {
            if (! $request->hasFile($field)) {
                continue;
            }

            $file = $request->file($field);
            if (! $file || ! $file->isValid()) {
                continue;
            }

            $ext = strtolower($file->getClientOriginalExtension() ?: '');
            $ext = $ext !== '' ? $ext : 'bin';
            $filename = $field.'.'.$ext;

            // Overwrite existing file for deterministic URLs.
            $file->move($publicDir, $filename);

            // Store path relative to /public.
            $assets[$field] = 'branding/'.$brandKey.'/'.$filename;
        }

        $palette = (array) $request->input('palette', []);
        foreach ($palette as $k => $v) {
            $v = is_string($v) ? trim($v) : '';
            if ($v === '') {
                continue;
            }
            $palette[$k] = str_starts_with($v, '#') ? $v : ('#'.$v);
        }

        $row->fill([
            'name' => $request->string('name')->toString(),
            'assets' => $assets,
            'palette' => $palette,
            'texts' => (array) $request->input('texts', []),
        ]);

        $row->save();

        // Clear cache for this brand.
        Cache::forget('branding:public:'.$brandKey);

        return redirect()
            ->route('admin.branding.edit', ['brandKey' => $brandKey])
            ->with('status', 'Branding actualizado');
    }

    public function activate(Request $request, string $brandKey)
    {
        if (! config('branding.runtime_switch', false)) {
            return redirect()
                ->route('admin.branding.edit', ['brandKey' => $brandKey])
                ->with('status', 'Runtime switch deshabilitado. El brand activo se controla con APP_BRAND.');
        }

        $defaults = (array) config('branding.brands.'.$brandKey, []);
        if (empty($defaults)) {
            abort(404, 'Brand desconocido: '.$brandKey);
        }

        // Ensure the row exists.
        Branding::firstOrCreate(['key' => $brandKey], ['name' => (string) Arr::get($defaults, 'name', $brandKey)]);

        Branding::query()->update(['is_active' => false]);
        Branding::query()->where('key', $brandKey)->update(['is_active' => true]);

        // Clear cache for all brands so the UI switches immediately.
        foreach (array_keys((array) config('branding.brands', [])) as $k) {
            Cache::forget('branding:public:'.$k);
        }
        // Also clear global cache entries if any exist.
        Cache::forget('branding:activeKey');

        return redirect()
            ->route('admin.branding.edit', ['brandKey' => $brandKey])
            ->with('status', 'Brand activo cambiado a: '.$brandKey);
    }
}
