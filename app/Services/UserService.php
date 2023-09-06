<?php
/**
 * AUTH SVOI.
 *
 * @author  Mirfayz Nosirov
 * Created: 07.09.2023 / 02:30
 */

namespace App\Services;

use App\Http\DTO\UserDto;
use App\Models\User;

class UserService
{
    /**
     * @return static
     */
    public static function getInstance(): static
    {
        return new static();
    }

    public function store(UserDto $dto)
    {
        User::query()->create(
            [
                'login'            => $dto->getLogin(),
                'password'         => bcrypt($dto->getPassword()),
                'telephone_number' => $dto->getTelephoneNumber(),
                'status'           => $dto->getStatus(),
                'auth_step'        => $dto->getAuthStep(),
                'role_id'          => $dto->getRoleId()
            ]
        );
    }
}
