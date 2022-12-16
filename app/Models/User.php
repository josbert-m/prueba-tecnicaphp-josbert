<?php

namespace App\Models;

use App\Base\Database\Model;

class User extends Model
{
    protected array $hidden = [
        'password'
    ];

    public int $id;

    public string $firstname;

    public string $lastname;

    public string $email;

    public string $password;
}
