<?php
/**
 * AUTH SVOI.
 *
 * @author  Mirfayz Nosirov
 * Created: 07.09.2023 / 02:37
 */

namespace App\Http\Middleware;

use App\Models\User;
use Closure;

class Admin
{
    public function handle($request, Closure $next)
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->role->getName() === 'admin') {
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
