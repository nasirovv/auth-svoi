<?php
/**
 * AUTH SVOI.
 *
 * @author  Mirfayz Nosirov
 * Created: 07.09.2023 / 01:42
 */

namespace App\Http\Middleware;

use App\Models\User;
use Closure;

class CheckRole
{
    public function handle($request, Closure $next)
    {
        /** @var User $user */
        $user = $request->user();

        if (in_array($user->role->getName(), ['moderator', 'admin'])) {
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
