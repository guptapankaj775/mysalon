<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class SetCurrentSalon
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $slug = $request->route('salon');
        $isFromRoute = true;

        if (!$slug && \Illuminate\Support\Facades\Auth::check()) {
            $user = \Illuminate\Support\Facades\Auth::user();
            $slug = $user->slug;
            if (!$slug && $user->created_by) {
                $owner = User::find($user->created_by);
                if ($owner) {
                    $slug = $owner->slug;
                }
            }
            $isFromRoute = false;
        }

        if ($slug) {
            $salon = $isFromRoute 
                ? User::where('slug', $slug)->firstOrFail() 
                : User::where('slug', $slug)->first();

            if ($salon) {
                // Share with all views
                view()->share('currentSalon', $salon);
                // Bind to request for controllers
                $request->attributes->set('salon', $salon);
                $request->attributes->set('is_salon_route', $isFromRoute);
                
                $user = \Illuminate\Support\Facades\Auth::user();
                $isSuperAdmin = $user && $user->isAdmin();

                // Set default route parameter for URL generation (only for salon routes or non-super-admins)
                if ($isFromRoute || !$isSuperAdmin) {
                    \Illuminate\Support\Facades\URL::defaults(['salon' => $slug]);
                }

                // Forget the salon parameter from the route so it does not pollute method signatures
                if ($request->route()) {
                    $request->route()->forgetParameter('salon');
                }

                // If it is a GET request to a global /admin URL, redirect to the salon-scoped portal URL for non-super-admins
                if (!app()->environment('testing') && !$isFromRoute && $request->isMethod('GET') && $request->is('admin*') && !$request->is('admin/login*')) {
                    if (\Illuminate\Support\Facades\Auth::check() && !$isSuperAdmin) {
                        $path = $request->getPathInfo();
                        $portalPath = preg_replace('/^\/admin/', '/portal', $path);
                        $newPath = '/' . $slug . $portalPath;
                        $queryString = $request->getQueryString();
                        if ($queryString) {
                            $newPath .= '?' . $queryString;
                        }
                        return redirect($newPath);
                    }
                }
            }
        }
        return $next($request);
    }
}
?>
