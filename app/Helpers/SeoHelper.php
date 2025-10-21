<?php

namespace App\Helpers;

use App\Models\Article;
use App\Models\Setting;

class SeoHelper
{
    /**
     * Generate meta title
     */
    public static function generateTitle($title = null, $suffix = null)
    {
        $siteName = SettingsHelper::siteName();
        
        if ($title) {
            $title = $title . ' - ' . $siteName;
        } else {
            $title = $siteName;
        }
        
        if ($suffix) {
            $title = $title . ' | ' . $suffix;
        }
        
        return $title;
    }

    /**
     * Generate meta description
     */
    public static function generateDescription($description = null, $default = null)
    {
        if ($description) {
            return substr(strip_tags($description), 0, 160);
        }
        
        if ($default) {
            return substr(strip_tags($default), 0, 160);
        }
        
        return SettingsHelper::siteDescription();
    }

    /**
     * Generate meta keywords
     */
    public static function generateKeywords($keywords = [])
    {
        $defaultKeywords = explode(',', SettingsHelper::metaKeywords());
        $allKeywords = array_merge($defaultKeywords, $keywords);
        
        return implode(', ', array_unique(array_filter($allKeywords)));
    }

    /**
     * Generate Open Graph data
     */
    public static function generateOpenGraph($data = [])
    {
        $defaults = [
            'og:title' => SettingsHelper::siteName(),
            'og:description' => SettingsHelper::siteDescription(),
            'og:image' => SettingsHelper::siteLogo(),
            'og:url' => request()->url(),
            'og:type' => 'website',
            'og:site_name' => SettingsHelper::siteName(),
        ];
        
        return array_merge($defaults, $data);
    }

    /**
     * Generate Twitter Card data
     */
    public static function generateTwitterCard($data = [])
    {
        $defaults = [
            'twitter:card' => 'summary_large_image',
            'twitter:title' => SettingsHelper::siteName(),
            'twitter:description' => SettingsHelper::siteDescription(),
            'twitter:image' => SettingsHelper::siteLogo(),
        ];
        
        return array_merge($defaults, $data);
    }

    /**
     * Generate structured data for article
     */
    public static function generateArticleStructuredData(Article $article)
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $article->title,
            'description' => $article->excerpt,
            'image' => $article->featured_image ? asset('storage/' . $article->featured_image) : asset('images/default-news.jpg'),
            'author' => [
                '@type' => 'Person',
                'name' => $article->author->name ?? 'Admin'
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => SettingsHelper::siteName(),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => SettingsHelper::siteLogo()
                ]
            ],
            'datePublished' => $article->published_at ? $article->published_at->toISOString() : $article->created_at->toISOString(),
            'dateModified' => $article->updated_at->toISOString(),
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => route('articles.show', $article)
            ]
        ];
    }

    /**
     * Generate breadcrumb structured data
     */
    public static function generateBreadcrumbStructuredData($items)
    {
        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => []
        ];
        
        foreach ($items as $index => $item) {
            $structuredData['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['name'],
                'item' => $item['url'] ?? null
            ];
        }
        
        return $structuredData;
    }

    /**
     * Generate canonical URL
     */
    public static function generateCanonicalUrl($url = null)
    {
        return $url ?: request()->url();
    }

    /**
     * Generate robots meta
     */
    public static function generateRobotsMeta($index = true, $follow = true)
    {
        $robots = [];
        
        if ($index) {
            $robots[] = 'index';
        } else {
            $robots[] = 'noindex';
        }
        
        if ($follow) {
            $robots[] = 'follow';
        } else {
            $robots[] = 'nofollow';
        }
        
        return implode(', ', $robots);
    }
}
