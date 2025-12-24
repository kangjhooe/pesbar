<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ActivityLogHelper
{
    /**
     * Log user activity
     */
    public static function log(string $action, string $description = null, array $context = [])
    {
        $user = Auth::user();
        
        $logData = [
            'action' => $action,
            'description' => $description,
            'user_id' => $user ? $user->id : null,
            'user_name' => $user ? $user->name : 'Guest',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'context' => $context,
            'timestamp' => now()->toDateTimeString(),
        ];
        
        Log::channel('activity')->info($action, $logData);
    }
    
    /**
     * Log article activity
     */
    public static function logArticle(string $action, $article, string $description = null)
    {
        self::log($action, $description, [
            'article_id' => $article->id,
            'article_title' => $article->title,
            'article_slug' => $article->slug,
        ]);
    }
    
    /**
     * Log user management activity
     */
    public static function logUser(string $action, $user, string $description = null)
    {
        self::log($action, $description, [
            'target_user_id' => $user->id,
            'target_user_name' => $user->name,
            'target_user_email' => $user->email,
        ]);
    }
    
    /**
     * Log admin activity
     */
    public static function logAdmin(string $action, string $description = null, array $context = [])
    {
        $user = Auth::user();
        
        if (!$user || !$user->isAdmin()) {
            return;
        }
        
        self::log("admin.{$action}", $description, $context);
    }
    
    /**
     * Log security event
     */
    public static function logSecurity(string $action, string $description = null, array $context = [])
    {
        $logData = [
            'action' => "security.{$action}",
            'description' => $description,
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'context' => $context,
            'timestamp' => now()->toDateTimeString(),
        ];
        
        Log::channel('activity')->warning("security.{$action}", $logData);
    }
}

