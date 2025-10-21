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
        return self::get('site_name', 'Portal Berita');
    }

    /**
     * Get site description
     */
    public static function siteDescription()
    {
        return self::get('site_description', 'Portal berita resmi');
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
        return (bool) self::get('auto_approve_comments', false);
    }

    public static function requireCommentApproval()
    {
        return (bool) self::get('require_comment_approval', true);
    }

    public static function enableRegistration()
    {
        return (bool) self::get('enable_registration', true);
    }

    public static function enableNewsletter()
    {
        return (bool) self::get('enable_newsletter', true);
    }

    public static function maintenanceMode()
    {
        return (bool) self::get('maintenance_mode', false);
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
