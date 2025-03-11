<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class GlobalDataServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Combine multiple stats into one cache key to avoid multiple queries
        View::composer('*', function($view) {
            $stats = Cache::remember('siteStats', 600, function () {
                $totalUsers = User::count();
                $activeUsers = User::where('is_active', 1)->count();
                $inactiveUsers = $totalUsers - $activeUsers;

                $totalPosts = Post::count();
                $publishedPosts = Post::where('is_published', 1)->count();
                $unpublishedPosts = $totalPosts - $publishedPosts;

                return compact('totalUsers', 'activeUsers', 'inactiveUsers', 'totalPosts', 'publishedPosts', 'unpublishedPosts');
            });

            // Pass all stats to the view
            $view->with($stats);
        });
    }
}
