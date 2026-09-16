<?php

namespace App\Helpers;

use App\Models\Setting;

class SettingsHelper
{
    /**
     * Get setting value by key
     */
    public static function get($key, $default = null)
    {
        return Setting::get($key, $default);
    }

    /**
     * Set setting value by key
     */
    public static function set($key, $value)
    {
        return Setting::set($key, $value);
    }

    /**
     * Get site name
     */
    public static function siteName()
    {
        return self::get('site_name', 'Pesisir Barat Hub');
    }

    /**
     * Get site description
     */
    public static function siteDescription()
    {
        return self::get('site_description', 'Platform informasi Kabupaten Pesisir Barat');
    }

    /**
     * Get site logo
     */
    public static function siteLogo()
    {
        $logo = self::get('site_logo');
        return $logo ? asset('storage/' . $logo) : asset('images/logo-pesisir-barat.png');
    }

    /**
     * Get site favicon
     */
    public static function siteFavicon()
    {
        $favicon = self::get('site_favicon');
        return $favicon ? asset('storage/' . $favicon) : asset('favicon.ico');
    }

    /**
     * Get contact information
     */
    public static function contactEmail()
    {
        return self::get('contact_email', 'info@example.com');
    }

    public static function contactPhone()
    {
        return self::get('contact_phone', '');
    }

    public static function contactAddress()
    {
        return self::get('contact_address', '');
    }

    /**
     * Get social media URLs
     */
    public static function facebookUrl()
    {
        return self::get('facebook_url', '');
    }

    public static function twitterUrl()
    {
        return self::get('twitter_url', '');
    }

    public static function instagramUrl()
    {
        return self::get('instagram_url', '');
    }

    public static function youtubeUrl()
    {
        return self::get('youtube_url', '');
    }

    /**
     * Get about page content
     */
    public static function aboutTitle()
    {
        return self::get('about_title', 'Tentang Kami');
    }

    public static function aboutContent()
    {
        return self::get('about_content', '');
    }

    public static function aboutImage()
    {
        $image = self::get('about_image');
        return $image ? asset('storage/' . $image) : null;
    }

    public static function missionTitle()
    {
        return self::get('mission_title', 'Misi Kami');
    }

    public static function missionContent()
    {
        return self::get('mission_content', '');
    }

    public static function visionTitle()
    {
        return self::get('vision_title', 'Visi Kami');
    }

    public static function visionContent()
    {
        return self::get('vision_content', '');
    }

    /**
     * Get SEO settings
     */
    public static function metaTitle()
    {
        return self::get('meta_title', self::siteName());
    }

    public static function metaDescription()
    {
        return self::get('meta_description', self::siteDescription());
    }

    public static function metaKeywords()
    {
        return self::get('meta_keywords', '');
    }

    public static function googleAnalytics()
    {
        return self::get('google_analytics', '');
    }

    public static function googleSearchConsole()
    {
        return self::get('google_search_console', '');
    }

    public static function facebookPixel()
    {
        return self::get('facebook_pixel', '');
    }

    /**
     * Get system settings
     */
    public static function articlesPerPage()
    {
        return (int) self::get('articles_per_page', 10);
    }

    public static function commentsPerPage()
    {
        return (int) self::get('comments_per_page', 10);
    }

    public static function autoApproveComments()
    {
        return self::asBool(self::get('auto_approve_comments'), false);
    }

    public static function requireCommentApproval()
    {
        return self::asBool(self::get('require_comment_approval'), true);
    }

    /**
     * Default approval for comments.
     * Login users are always approved; guests always require moderation.
     * Legacy settings are ignored for the public comment flow.
     */
    public static function commentsApprovedByDefault(bool $isGuest = false): bool
    {
        if ($isGuest) {
            return false;
        }

        return true;
    }

    public static function enableRegistration()
    {
        return self::asBool(self::get('enable_registration'), true);
    }

    public static function enableNewsletter()
    {
        return self::asBool(self::get('enable_newsletter'), true);
    }

    public static function maintenanceMode()
    {
        return self::asBool(self::get('maintenance_mode'), false);
    }

    protected static function asBool($value, bool $default = false): bool
    {
        if ($value === null || $value === '') {
            return $default;
        }

        $parsed = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        return $parsed === null ? $default : $parsed;
    }

    /**
     * Get editorial team settings
     */
    public static function editorialTeamTitle()
    {
        return self::get('editorial_team_title', 'Tim Redaksi');
    }

    public static function editorialTeamContent()
    {
        $content = self::get('editorial_team_content', '[]');
        return json_decode($content, true) ?: [];
    }
}
