<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Route;

class Breadcrumbs
{
    public static function generate()
    {
        $segments = request()->segments();
        $breadcrumbs = [];
        $path = '';

        foreach ($segments as $index => $segment) {
            $path .= '/' . $segment;

            // Special case: Backend index route (change 'backend' to 'Home')
            if ($path === '/backend') {
                $label = 'Home';
                $routeName = 'backendindex';
            } else {
                $routeName = Route::getRoutes()->match(request()->create($path))->getName();
                $label = ucfirst(str_replace('-', ' ', $segment));
            }

            $breadcrumbs[] = [
                'label' => $label,
                'url' => $routeName ? route($routeName) : url($path)
            ];
        }

        return $breadcrumbs;
    }
}
