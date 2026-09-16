<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Helpers\ActivityLogHelper;
use App\Helpers\CacheHelper;
use App\Helpers\UploadValidation;
use Illuminate\Support\Facades\Storage;

class AdminSettingsController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        $settings = Setting::all()->pluck('setting_value', 'setting_key');

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update general settings
     */
    public function updateGeneral(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'required|string|max:500',
            'site_keywords' => 'nullable|string|max:500',
            'site_author' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_address' => 'nullable|string|max:500',
            'facebook_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
        ]);

        $settings = [
            'site_name' => $request->site_name,
            'site_description' => $request->site_description,
            'site_keywords' => $request->site_keywords,
            'site_author' => $request->site_author,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
            'contact_address' => $request->contact_address,
            'facebook_url' => $request->facebook_url,
            'twitter_url' => $request->twitter_url,
            'instagram_url' => $request->instagram_url,
            'youtube_url' => $request->youtube_url,
        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }

        ActivityLogHelper::log('settings.general', 'Pengaturan umum diperbarui');

        return back()->with('success', 'Pengaturan umum berhasil diperbarui!');
    }

    /**
     * Update logo
     */
    public function updateLogo(Request $request)
    {
        try {
            $request->validate([
                'site_logo' => UploadValidation::logo(),
                'site_favicon' => UploadValidation::favicon(),
            ], [
                'site_logo.image' => 'Logo harus berupa file gambar yang valid.',
                'site_logo.mimes' => 'Logo harus berformat: JPEG, PNG, JPG, atau WebP',
                'site_logo.max' => 'Ukuran logo maksimal 2MB',
                'site_favicon.mimes' => 'Favicon harus berformat: JPEG, PNG, JPG, ICO, atau WebP',
                'site_favicon.max' => 'Ukuran favicon maksimal 512KB',
            ]);

            $updated = false;

            if ($request->hasFile('site_logo')) {
                $oldLogo = Setting::get('site_logo');
                if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                    Storage::disk('public')->delete($oldLogo);
                }

                Setting::set('site_logo', $request->file('site_logo')->store('settings', 'public'));
                $updated = true;
            }

            if ($request->hasFile('site_favicon')) {
                $oldFavicon = Setting::get('site_favicon');
                if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                    Storage::disk('public')->delete($oldFavicon);
                }

                Setting::set('site_favicon', $request->file('site_favicon')->store('settings', 'public'));
                $updated = true;
            }

            if (!$updated) {
                return back()->with('error', 'Pilih logo atau favicon untuk diunggah.');
            }

            ActivityLogHelper::log('settings.logo', 'Logo/favicon diperbarui');

            return back()->with('success', 'Logo berhasil diperbarui!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Logo upload error', ['message' => $e->getMessage()]);
            return back()->with('error', 'Terjadi kesalahan saat mengupload logo.');
        }
    }

    /**
     * Update about page content
     */
    public function updateAbout(Request $request)
    {
        $request->validate([
            'about_title' => 'required|string|max:255',
            'about_content' => 'required|string',
            'about_image' => UploadValidation::image(false, 2048),
            'mission_title' => 'nullable|string|max:255',
            'mission_content' => 'nullable|string',
            'vision_title' => 'nullable|string|max:255',
            'vision_content' => 'nullable|string',
        ]);

        foreach ([
            'about_title' => $request->about_title,
            'about_content' => $request->about_content,
            'mission_title' => $request->mission_title,
            'mission_content' => $request->mission_content,
            'vision_title' => $request->vision_title,
            'vision_content' => $request->vision_content,
        ] as $key => $value) {
            Setting::set($key, $value);
        }

        if ($request->hasFile('about_image')) {
            $oldImage = Setting::get('about_image');
            if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }
            Setting::set('about_image', $request->file('about_image')->store('settings', 'public'));
        }

        ActivityLogHelper::log('settings.about', 'Halaman tentang diperbarui');

        return back()->with('success', 'Halaman tentang berhasil diperbarui!');
    }

    /**
     * Update editorial team settings
     */
    public function updateEditorial(Request $request)
    {
        $request->validate([
            'editorial_team_title' => 'required|string|max:255',
            'editorial_team' => 'required|array|min:1',
            'editorial_team.*.name' => 'required|string|max:255',
            'editorial_team.*.position' => 'required|string|max:255',
            'editorial_team.*.description' => 'nullable|string|max:500',
        ]);

        $teamMembers = array_values(array_filter($request->editorial_team, function ($member) {
            return !empty($member['name']) && !empty($member['position']);
        }));

        Setting::set('editorial_team_title', $request->editorial_team_title);
        Setting::set('editorial_team_content', json_encode($teamMembers));

        ActivityLogHelper::log('settings.editorial', 'Tim redaksi diperbarui');

        return back()->with('success', 'Tim redaksi berhasil diperbarui!');
    }

    /**
     * Update SEO settings
     */
    public function updateSeo(Request $request)
    {
        $request->validate([
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            'google_analytics' => 'nullable|string|max:255',
            'google_search_console' => 'nullable|string|max:255',
            'facebook_pixel' => 'nullable|string|max:255',
        ]);

        foreach ([
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'google_analytics' => $request->google_analytics,
            'google_search_console' => $request->google_search_console,
            'facebook_pixel' => $request->facebook_pixel,
        ] as $key => $value) {
            Setting::set($key, $value);
        }

        ActivityLogHelper::log('settings.seo', 'Pengaturan SEO diperbarui');

        return back()->with('success', 'Pengaturan SEO berhasil diperbarui!');
    }

    /**
     * Update system settings
     */
    public function updateSystem(Request $request)
    {
        $request->validate([
            'articles_per_page' => 'required|integer|min:1|max:100',
            'comments_per_page' => 'required|integer|min:1|max:100',
            'auto_approve_comments' => 'boolean',
            'require_comment_approval' => 'boolean',
            'enable_registration' => 'boolean',
            'enable_newsletter' => 'boolean',
            'maintenance_mode' => 'boolean',
        ]);

        foreach ([
            'articles_per_page' => (string) $request->articles_per_page,
            'comments_per_page' => (string) $request->comments_per_page,
            'auto_approve_comments' => $request->boolean('auto_approve_comments'),
            'require_comment_approval' => $request->boolean('require_comment_approval'),
            'enable_registration' => $request->boolean('enable_registration'),
            'enable_newsletter' => $request->boolean('enable_newsletter'),
            'maintenance_mode' => $request->boolean('maintenance_mode'),
        ] as $key => $value) {
            Setting::set($key, $value);
        }

        ActivityLogHelper::log('settings.system', 'Pengaturan sistem diperbarui', [
            'maintenance_mode' => $request->boolean('maintenance_mode'),
            'enable_registration' => $request->boolean('enable_registration'),
        ]);

        return back()->with('success', 'Pengaturan sistem berhasil diperbarui!');
    }

    /**
     * Clear application caches (does not log users out).
     */
    public function clearCache()
    {
        \Artisan::call('cache:clear');
        \Artisan::call('config:clear');
        \Artisan::call('view:clear');
        \Artisan::call('route:clear');
        CacheHelper::clearSettingsCache();
        CacheHelper::clearArticleCache();
        CacheHelper::clearDashboardCache();

        ActivityLogHelper::log('settings.cache_cleared', 'Cache aplikasi dibersihkan');

        return back()->with('success', 'Cache berhasil dibersihkan!');
    }
}
