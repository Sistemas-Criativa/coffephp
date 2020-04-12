<?php

namespace Models;

use Core\Model;

class Session extends Model
{
    protected $table = "session";
    protected $hidden = ["id"];
    protected $fillables = ["id_User", "Token", "Status"];
}
