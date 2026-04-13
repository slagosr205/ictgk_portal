<?php

namespace App\Support\Branding;

use App\Models\Branding;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class BrandingManager
{
    public function activeKey(): string
    {
        $envKey = (string) config('branding.active', 'altia');

        if (! config('branding.runtime_switch', false)) {
            return $envKey;
        }

        // Runtime switch enabled: if DB has an active branding, use it.
        try {
            if (! Schema::hasTable('brandings')) {
                return $envKey;
            }
        } catch (\Throwable $e) {
            return $envKey;
        }

        $active = Branding::query()->where('is_active', true)->value('key');

        return $active ? (string) $active : $envKey;
    }

    public function publicFor(string $brandKey): array
    {
        $brandKey = trim($brandKey);
        if ($brandKey === '') {
            $brandKey = $this->activeKey();
        }

        return Cache::rememberForever('branding:public:'.$brandKey, function () use ($brandKey) {
            $brands = (array) config('branding.brands', []);

            // Defaults from config.
            $brand = $brands[$brandKey] ?? $brands['altia'] ?? null;

            // Override from DB when available.
            $dbBrand = $this->readFromDb($brandKey);
            if ($dbBrand) {
                $brand = array_replace_recursive((array) $brand, $dbBrand);
            }

            if (! $brand) {
                return [
                    'key' => $brandKey,
                    'name' => config('app.name', 'App'),
                    'assets' => [],
                    'palette' => [],
                    'texts' => [],
                    'cssVars' => [],
                ];
            }

            $palette = (array) Arr::get($brand, 'palette', []);
            $assets = (array) Arr::get($brand, 'assets', []);
            $texts = (array) Arr::get($brand, 'texts', []);

            $assetUrls = [];
            foreach ($assets as $k => $path) {
                $assetUrls[$k.'_path'] = $path;
                $assetUrls[$k.'_url'] = $this->resolveAssetUrl($path);
            }

            // Brand-prefixed variables. Existing CSS maps these to legacy vars.
            $cssVars = [
                '--brand-key' => (string) Arr::get($brand, 'key', $brandKey),
                '--brand-name' => (string) Arr::get($brand, 'name', $brandKey),
                '--brand-primary' => $this->normalizeHex((string) Arr::get($palette, 'primary', '#32C36C')),
                '--brand-secondary' => $this->normalizeHex((string) Arr::get($palette, 'secondary', '#1A2A36')),
                '--brand-accent' => $this->normalizeHex((string) Arr::get($palette, 'accent', '#DCE442')),
                '--brand-light' => $this->normalizeHex((string) Arr::get($palette, 'light', '#F6F7F8')),
                '--brand-dark' => $this->normalizeHex((string) Arr::get($palette, 'dark', '#1A2A36')),
                '--brand-danger' => $this->normalizeHex((string) Arr::get($palette, 'danger', '#E74C3C')),
                '--brand-success' => $this->normalizeHex((string) Arr::get($palette, 'success', '#32C36C')),
                '--brand-warning' => $this->normalizeHex((string) Arr::get($palette, 'warning', '#DCE442')),
                '--brand-text-primary' => $this->normalizeHex((string) Arr::get($palette, 'text_primary', '#2C3E50')),
                '--brand-text-secondary' => $this->normalizeHex((string) Arr::get($palette, 'text_secondary', '#6C757D')),
                '--brand-border' => $this->normalizeHex((string) Arr::get($palette, 'border', '#E1E8ED')),
            ];

            $cssVars['--brand-primary-rgb'] = $this->hexToRgbTriplet($cssVars['--brand-primary']);
            $cssVars['--brand-secondary-rgb'] = $this->hexToRgbTriplet($cssVars['--brand-secondary']);
            $cssVars['--brand-accent-rgb'] = $this->hexToRgbTriplet($cssVars['--brand-accent']);
            $cssVars['--brand-dark-rgb'] = $this->hexToRgbTriplet($cssVars['--brand-dark']);

            return [
                'key' => (string) Arr::get($brand, 'key', $brandKey),
                'name' => (string) Arr::get($brand, 'name', $brandKey),
                'assets' => $assetUrls,
                'palette' => $palette,
                'texts' => $texts,
                'cssVars' => $cssVars,
            ];
        });
    }

    /**
     * Resolved branding config for the active brand.
     * This structure is safe to expose to the frontend.
     */
    public function public(): array
    {
        return $this->publicFor($this->activeKey());
    }

    private function readFromDb(string $brandKey): ?array
    {
        try {
            if (! Schema::hasTable('brandings')) {
                return null;
            }
        } catch (\Throwable $e) {
            return null;
        }

        $row = Branding::query()->where('key', $brandKey)->first();
        if (! $row) {
            return null;
        }

        return [
            'key' => $row->key,
            'name' => $row->name,
            'assets' => (array) ($row->assets ?? []),
            'palette' => (array) ($row->palette ?? []),
            'texts' => (array) ($row->texts ?? []),
        ];
    }

    private function resolveAssetUrl(?string $path): ?string
    {
        $path = $path ? trim($path) : null;
        if (! $path) {
            return null;
        }

        if (preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }

        // If it's a public path (recommended): /public/branding/...
        if (str_starts_with($path, 'branding/') || str_starts_with($path, 'branding\\')) {
            $normalized = str_replace('\\', '/', $path);

            // Prefer /public if the file exists there (admin uploads).
            if (File::exists(public_path($normalized))) {
                return asset($normalized);
            }

            // Otherwise fall back to disk resolution below.
        }

        // Otherwise try the configured disk (legacy /storage/... style)
        $disk = (string) config('branding.upload_disk', 'public');
        try {
            $rel = Storage::disk($disk)->url($path); // usually /storage/...

            return URL::to($rel);
        } catch (\Throwable $e) {
            return asset($path);
        }
    }

    private function normalizeHex(string $value): string
    {
        $v = trim($value);
        if ($v === '') {
            return $v;
        }

        return str_starts_with($v, '#') ? $v : ('#'.$v);
    }

    private function hexToRgbTriplet(string $hex): string
    {
        $hex = ltrim(trim($hex), '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        if (strlen($hex) !== 6 || ! ctype_xdigit($hex)) {
            return '0 0 0';
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return $r.' '.$g.' '.$b;
    }
}
