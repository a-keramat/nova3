<?php

namespace Nova\Users\Actions;

use Nova\Users\Models\User;
use Nova\Users\DataTransferObjects\UserData;

class CreateUser
{
    public function execute(UserData $data): User
    {
        return User::create($data->toArray());
    }
}