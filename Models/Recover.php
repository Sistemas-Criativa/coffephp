<?php

namespace Models;

use Core\Model;

class Recover extends Model
{
    protected $table = "recover";
    protected $fillables = ['id_User', 'Token'];
}
