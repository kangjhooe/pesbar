<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class RobotsController extends Controller
{
    /**
     * Generate robots.txt dynamically
     */
    public function index(): Response
    {
        $appUrl = config('app.url', url('/'));
        $sitemapUrl = rtrim($appUrl, '/') . '/sitemap.xml';
        $newsSitemapUrl = rtrim($appUrl, '/') . '/sitemap-news.xml';
        
        $robots = "User-agent: *\n";
        $robots .= "Allow: /\n\n";
        $robots .= "# Disallow admin and private areas\n";
        $robots .= "Disallow: /admin/\n";
        $robots .= "Disallow: /dashboard/\n";
        $robots .= "Disallow: /penulis/\n";
        $robots .= "Disallow: /profile/\n";
        $robots .= "Disallow: /upgrade-request\n\n";
        $robots .= "# Allow public API endpoints\n";
        $robots .= "Allow: /api/widgets/\n\n";
        $robots .= "# Sitemap locations\n";
        $robots .= "Sitemap: {$sitemapUrl}\n";
        $robots .= "Sitemap: {$newsSitemapUrl}\n";
        
        return response($robots, 200)
            ->header('Content-Type', 'text/plain');
    }
}

