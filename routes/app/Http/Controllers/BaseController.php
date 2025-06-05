<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

class BaseController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $routeName = Route::currentRouteName();
            abort_unless(auth()->user()->can($routeName), 403);
            return $next($request);
        });
    }
}
