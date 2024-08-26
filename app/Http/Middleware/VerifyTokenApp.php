<?php
 
namespace App\Http\Middleware;
 
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
 
class VerifyTokenApp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->header('token') !== '360879|Df7GOPeMFBxKfJqoUh6ryQnSTWW7CNb8mVRcW2bf') {
            return redirect('/');
        }
 
        return $next($request);
    }
}