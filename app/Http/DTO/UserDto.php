<?php
/**
 * AUTH SVOI.
 *
 * @author  Mirfayz Nosirov
 * Created: 07.09.2023 / 02:31
 */

namespace App\Http\DTO;

class UserDto
{
    /** @var int $role_id */
    protected int $role_id;

    /** @var string $login */
    protected string $login;

    /** @var string $password */
    protected string $password;

    /** @var string $telephone_number */
    protected string $telephone_number;

    /** @var string $status */
    protected string $status;

    /** @var string $auth_step */
    protected string $auth_step;

    public function __construct(
        int    $role_id,
        string $login,
        string $password,
        string $telephone_number,
        string $status,
        string $auth_step,
    )
    {
        $this->role_id = $role_id;
        $this->login = $login;
        $this->password = $password;
        $this->telephone_number = $telephone_number;
        $this->status = $status;
        $this->auth_step = $auth_step;
    }

    /**
     * @return int
     */
    public function getRoleId(): int
    {
        return $this->role_id;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getTelephoneNumber(): string
    {
        return $this->telephone_number;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getAuthStep(): string
    {
        return $this->auth_step;
    }
}
