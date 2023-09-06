<?php
/**
 * AUTH SVOI.
 *
 * @author  Mirfayz Nosirov
 * Created: 07.09.2023 / 02:22
 */

namespace App\Http\Controllers;

use App\Http\DTO\UserDto;
use App\Services\UserService;
use Exception;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Rule;
use RuntimeException;

/**
 * Class AdminController
 * @package App\Http\Controllers
 */
class AdminController extends Controller
{
    public function validator(Request $request): ValidatorContract
    {
        return ValidatorFacade::make(
            $request->all(),
            [
                'phone'    => 'required|numeric|unique:users,telephone_number|digits:12',
                'login'    => 'required|string|min:6',
                'password' => 'required|string|min:6',
                'status'   => ['required', Rule::in(['active', 'block'])],
                'role_id'  => 'required|int'

            ],
            [
                'required' => 'Поле, обязательное для заполнения',
            ]
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addUser(Request $request): JsonResponse
    {
        $message = null;
        $code = 200;

        try {
            DB::beginTransaction();

            /** @var \Illuminate\Validation\Validator $validator */
            $validator = $this->validator($request);

            if ($validator->fails()) {
                throw new RuntimeException($validator->errors()->first(), 400);
            }

            $data = $validator->getData();

            UserService::getInstance()->store(
                new UserDto(
                    $data['role_id'],
                    $data['login'],
                    $data['password'],
                    $data['phone'],
                    $data['status'],
                    'login'
                )
            );

            DB::commit();

            return $this->render_json([], 'Successfully added');
        } catch (Exception $exception) {
            DB::rollBack();
            $message = $exception->getMessage();
            $code = (int)$exception->getCode() === 0 ? 400 : (int)$exception->getCode();
        }

        return $this->render_json([], $message, $code);
    }
}
