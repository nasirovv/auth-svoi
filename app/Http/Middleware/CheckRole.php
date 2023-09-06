<?php
/**
 * AUTH SVOI.
 *
 * @author  Mirfayz Nosirov
 * Created: 07.09.2023 / 01:42
 */

namespace App\Http\Middleware;

use Closure;

class CheckRole
{
    public function handle($request, Closure $next)
    {
        if ($request->user()->tokenCan('role:admin') || $request->user()->tokenCan('role:moderator')) {
            return $next($request);
        }

        return response()->json(
            [
                'code'    => 401,
                'message' => 'No Access!',
            ]
        );
    }
}
