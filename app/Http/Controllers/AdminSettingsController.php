<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AdminSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

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

        return back()->with('success', 'Pengaturan umum berhasil diperbarui!');
    }

    /**
     * Update logo
     */
    public function updateLogo(Request $request)
    {
        try {
            // Debug: Log request data
            \Log::info('Logo upload request', [
                'has_site_logo' => $request->hasFile('site_logo'),
                'has_site_favicon' => $request->hasFile('site_favicon'),
                'site_logo_size' => $request->hasFile('site_logo') ? $request->file('site_logo')->getSize() : null,
                'site_favicon_size' => $request->hasFile('site_favicon') ? $request->file('site_favicon')->getSize() : null,
            ]);

            $request->validate([
                'site_logo' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
                'site_favicon' => 'nullable|file|mimes:jpeg,png,jpg,gif,ico,svg|max:512',
            ], [
                'site_logo.mimes' => 'Logo harus berupa file gambar dengan format: JPEG, PNG, JPG, GIF, SVG, atau WebP',
                'site_logo.max' => 'Ukuran logo maksimal 2MB',
                'site_favicon.mimes' => 'Favicon harus berupa file gambar dengan format: JPEG, PNG, JPG, GIF, ICO, atau SVG',
                'site_favicon.max' => 'Ukuran favicon maksimal 512KB',
            ]);

            if ($request->hasFile('site_logo')) {
                $logoFile = $request->file('site_logo');
                
                // Debug: Log file details
                \Log::info('Logo file details', [
                    'original_name' => $logoFile->getClientOriginalName(),
                    'mime_type' => $logoFile->getMimeType(),
                    'size' => $logoFile->getSize(),
                    'extension' => $logoFile->getClientOriginalExtension(),
                ]);

                // Delete old logo
                $oldLogo = Setting::get('site_logo');
                if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                    Storage::disk('public')->delete($oldLogo);
                }

                $logoPath = $logoFile->store('settings', 'public');
                Setting::set('site_logo', $logoPath);
                
                \Log::info('Logo uploaded successfully', ['path' => $logoPath]);
            }

            if ($request->hasFile('site_favicon')) {
                $faviconFile = $request->file('site_favicon');
                
                // Debug: Log file details
                \Log::info('Favicon file details', [
                    'original_name' => $faviconFile->getClientOriginalName(),
                    'mime_type' => $faviconFile->getMimeType(),
                    'size' => $faviconFile->getSize(),
                    'extension' => $faviconFile->getClientOriginalExtension(),
                ]);

                // Delete old favicon
                $oldFavicon = Setting::get('site_favicon');
                if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                    Storage::disk('public')->delete($oldFavicon);
                }

                $faviconPath = $faviconFile->store('settings', 'public');
                Setting::set('site_favicon', $faviconPath);
                
                \Log::info('Favicon uploaded successfully', ['path' => $faviconPath]);
            }

            return back()->with('success', 'Logo berhasil diperbarui!');
            
        } catch (\Exception $e) {
            \Log::error('Logo upload error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Terjadi kesalahan saat mengupload logo: ' . $e->getMessage());
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
            'about_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'mission_title' => 'nullable|string|max:255',
            'mission_content' => 'nullable|string',
            'vision_title' => 'nullable|string|max:255',
            'vision_content' => 'nullable|string',
        ]);

        $settings = [
            'about_title' => $request->about_title,
            'about_content' => $request->about_content,
            'mission_title' => $request->mission_title,
            'mission_content' => $request->mission_content,
            'vision_title' => $request->vision_title,
            'vision_content' => $request->vision_content,
        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }

        // Handle about image upload
        if ($request->hasFile('about_image')) {
            // Delete old image
            $oldImage = Setting::get('about_image');
            if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }

            $imagePath = $request->file('about_image')->store('settings', 'public');
            Setting::set('about_image', $imagePath);
        }

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

        // Filter out empty members
        $teamMembers = array_filter($request->editorial_team, function($member) {
            return !empty($member['name']) && !empty($member['position']);
        });

        $settings = [
            'editorial_team_title' => $request->editorial_team_title,
            'editorial_team_content' => json_encode(array_values($teamMembers)),
        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }

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

        $settings = [
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'google_analytics' => $request->google_analytics,
            'google_search_console' => $request->google_search_console,
            'facebook_pixel' => $request->facebook_pixel,
        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Pengaturan SEO berhasil diperbarui!');
    }

    /**
     * Update email settings
     */
    public function updateEmail(Request $request)
    {
        $request->validate([
            'mail_from_name' => 'required|string|max:255',
            'mail_from_address' => 'required|email|max:255',
            'mail_host' => 'required|string|max:255',
            'mail_port' => 'required|integer|min:1|max:65535',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|string|in:tls,ssl',
        ]);

        $settings = [
            'mail_from_name' => $request->mail_from_name,
            'mail_from_address' => $request->mail_from_address,
            'mail_host' => $request->mail_host,
            'mail_port' => $request->mail_port,
            'mail_username' => $request->mail_username,
            'mail_password' => $request->mail_password,
            'mail_encryption' => $request->mail_encryption,
        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Pengaturan email berhasil diperbarui!');
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

        $settings = [
            'articles_per_page' => $request->articles_per_page,
            'comments_per_page' => $request->comments_per_page,
            'auto_approve_comments' => $request->boolean('auto_approve_comments'),
            'require_comment_approval' => $request->boolean('require_comment_approval'),
            'enable_registration' => $request->boolean('enable_registration'),
            'enable_newsletter' => $request->boolean('enable_newsletter'),
            'maintenance_mode' => $request->boolean('maintenance_mode'),
        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Pengaturan sistem berhasil diperbarui!');
    }

    /**
     * Clear cache
     */
    public function clearCache()
    {
        \Artisan::call('cache:clear');
        \Artisan::call('config:clear');
        \Artisan::call('view:clear');
        \Artisan::call('route:clear');

        return back()->with('success', 'Cache berhasil dibersihkan!');
    }
}
