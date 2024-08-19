<?php

namespace App\Http\Middleware;

use App\Enums\Role\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AdminMiddleware
 */
class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->role !== Role::ADMIN) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
