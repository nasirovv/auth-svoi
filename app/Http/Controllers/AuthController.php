<?php
/**
 * AUTH SVOI.
 *
 * @author  Mirfayz Nosirov
 * Created: 06.09.2023 / 01:59
 */

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Cache\RateLimiter;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;
use RuntimeException;

/**
 * Class AuthController
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{
    /**
     * @param Request $request
     *
     * @return ValidatorContract
     */
    protected function validator(Request $request): ValidatorContract
    {
        return ValidatorFacade::make(
            $request->all(),
            [
                'phone' => 'required|numeric|digits:12'
            ],
            [
                'numeric' => 'Номер телефона должен быть только цифровым',
                'digits'  => 'Номер телефона должен состоять из 12 символов',
            ]
        );
    }

    /**
     * @param Request $request
     * @return ValidatorContract
     */
    protected function validatorForConfirm(Request $request): ValidatorContract
    {
        return ValidatorFacade::make(
            $request->all(),
            [
                'phone' => 'required|numeric|digits:12',
                'code'  => 'required|numeric|digits:6'
            ],
            [
                'required' => 'Поле, обязательное для заполнения',
                'numeric'  => 'Данные должны быть только числовыми',
                'digits'   => 'Код должен состоять из 6 символов',
            ]
        );
    }

    /**
     * @param Request $request
     * @return ValidatorContract
     */
    public function validatorForLogin(Request $request): ValidatorContract
    {
        return ValidatorFacade::make(
            $request->all(),
            [
                'login'    => 'required|string|min:6',
                'password' => 'required|string|min:6',
                'user_id'  => 'required|int'
            ],
            [
                'required' => 'Поле, обязательное для заполнения',
            ]
        );
    }

    /**
     * @param Request $request
     * @return ValidatorContract
     */
    public function validatorForRegister(Request $request): ValidatorContract
    {
        return ValidatorFacade::make(
            $request->all(),
            [
                'login'                 => 'required|string|min:6',
                'password'              => 'required|string|min:6|confirmed',
                'password_confirmation' => 'required|string|min:6',
                'user_id'               => 'required|int'
            ],
            [
                'required' => 'Поле, обязательное для заполнения',
            ]
        );
    }

    protected function limiter(): RateLimiter
    {
        return app(RateLimiter::class);
    }

    protected function throttleKey(string $username, string $ip): string
    {
        return Str::lower($username) . '_' . str_replace('.', '_', $ip);
    }

    protected function hasTooManyLoginAttempt(string $throttle_key): bool
    {
        return $this->limiter()->tooManyAttempts($throttle_key, 3);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function code(Request $request): JsonResponse
    {
        $message = null;
        $code = 200;

        try {
            /** @var \Illuminate\Validation\Validator $validator */
            $validator = $this->validator($request);

            if ($validator->fails()) {
                throw new RuntimeException($validator->errors()->first(), 400);
            }

            $phone = $validator->validated()['phone'];

            if ($this->hasTooManyLoginAttempt($this->throttleKey($phone, $request->ip()))) {
                return $this->render_json([], 'Too many requests', 429);
            }

            $this->limiter()->hit($this->throttleKey($phone, $request->ip()), 60);
//            $rand = rand(100000, 999999);
            $rand = 222333;

            // SEND SMS CODE

            Cache::put($phone, Hash::make((string)$rand), now()->addMinutes(3));

            return $this->render_json([], 'Код успешно отправлено');
        } catch (Exception $exception) {
            $message = $exception->getMessage();
            $code = $exception->getCode() === 0 ? 400 : $exception->getCode();
        }

        return $this->render_json([], $message, $code);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function confirm(Request $request): JsonResponse
    {
        $message = null;
        $code = 200;

        try {
            $validator = $this->validatorForConfirm($request);

            if ($validator->fails()) {
                throw new RuntimeException($validator->errors()->first(), 400);
            }

            if (Hash::check($validator->getData()['code'], Cache::get($phone = $validator->getData()['phone']))) {
                /** @var User $user */
                $user = User::query()
                    ->where('telephone_number', '=', $phone)
                    ->with('role')
                    ->first();

                $is_new = 0; // go to login page

                if (is_null($user)) {
                    $user = User::query()->create(
                        [
                            'role_id'          => User::ROLES['user'],
                            'telephone_number' => $phone,
                            'status'           => 'active',
                            'auth_step'        => 'phone'
                        ]
                    );

                    $is_new = 1; // go to register page
                } else {
                    $user->fill(['auth_step' => 'phone'])->save();
                }

                Cache::forget($user->getTelephoneNumber());

                return $this->render_json(
                    [
                        'id'     => $user->getId(),
                        'is_new' => $is_new
                    ],
                    'Success');
            }

            return $this->render_json([], 'Код не совпадает', 400);
        } catch (Exception $exception) {
            $message = $exception->getMessage();
            $code = $exception->getCode() === 0 ? 400 : $exception->getCode();
        }

        return $this->render_json([], $message, $code);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $message = null;
        $code = 200;

        try {
            $validator = $this->validatorForLogin($request);

            if ($validator->fails()) {
                throw new RuntimeException($validator->errors()->first(), 400);
            }
            $data = $validator->getData();

            /** @var User $user */
            $user = User::query()
                ->whereKey((int)$data['user_id'])
                ->with('role')
                ->where('auth_step', '=', 'phone')
                ->first();

            if (is_null($user)) {
                throw new RuntimeException('User not found', 404);
            }

            if ($data['login'] === $user->getLogin() && Hash::check($data['password'], $user->getPassword())) {
                $user->fill(['auth_step' => 'login'])->save();

                $role = $user->role->getName();
                $token = $user->createToken('auth', ["role:$role"])->plainTextToken;
            } else {
                throw new RuntimeException('Credentials does not match', 404);
            }

            return $this->render_json(
                [
                    'token' => $token,
                ],
                'Successfully logged in!');
        } catch (Exception $exception) {
            $message = $exception->getMessage();
            $code = $exception->getCode() === 0 ? 400 : $exception->getCode();
        }

        return $this->render_json([], $message, $code);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $message = null;
        $code = 200;

        try {
            $validator = $this->validatorForRegister($request);

            if ($validator->fails()) {
                throw new RuntimeException($validator->errors()->first(), 400);
            }
            $data = $validator->getData();

            /** @var User $user */
            $user = User::query()
                ->whereKey((int)$data['user_id'])
                ->with('role')
                ->where('auth_step', '=', 'phone')
                ->first();

            if (is_null($user)) {
                throw new RuntimeException('User not found', 404);
            }

            $user->update(
                [
                    'login'     => $data['login'],
                    'password'  => bcrypt($data['password']),
                    'auth_step' => 'login'
                ]
            );

            $role = $user->role->getName();

            $token = $user->createToken('auth', ["role:$role"])->plainTextToken;

            return $this->render_json(
                [
                    'token' => $token
                ],
                'Successfully registered in!');
        } catch (Exception $exception) {
            $message = $exception->getMessage();
            $code = $exception->getCode() === 0 ? 400 : $exception->getCode();
        }

        return $this->render_json([], $message, $code);
    }
}
