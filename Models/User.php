<?php
namespace Models;
use Core\Model;
class User extends Model{
    protected $table = "user";
    protected $hidden = ["Password"];
    protected $fillables = ['Name','Email','User','Password'];
}
?>