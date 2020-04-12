<?php
namespace Models;
use Core\Model;
class Sessions extends Model{
    protected $table = "sessions";
    protected $hidden = ["id"];
    protected $fillables = ["id_User","Token","Status"];
}
?>