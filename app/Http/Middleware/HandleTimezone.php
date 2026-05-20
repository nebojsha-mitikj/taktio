<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use DateTimeZone;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleTimezone
{
    public function handle(Request $request, Closure $next): Response
    {
        $tz = $request->header('X-Timezone', config('app.timezone', 'UTC'));

        if (in_array($tz, DateTimeZone::listIdentifiers())) {
            date_default_timezone_set($tz);
        }

        return $next($request);
    }
}
