<?php
/**
 * AUTH SVOI.
 *
 * @author  Mirfayz Nosirov
 * Created: 06.09.2023 / 01:59
 */

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

/**
 * Class AuthController
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{

    // TODO throttle key for sms code

    public function code(Request $request)
    {
        $message = null;
        $code = 200;

        try {

        } catch (Exception $exception) {
            $message = $exception->getMessage();
            $code = $exception->getCode() === 0 ? 400 : $exception->getCode();
        }

        return $this->render_json([], $message, $code);
    }


    public function register(Request $request)
    {
        dd(123);
    }

    public function login(Request $request)
    {

    }
}
