<?php
// declare(strict_types=1);
namespace Core;

class Converters
{
   static function json($object, $encode = true){
        return $encode ? json_encode($object) : json_decode($object, true);
   }

   static function bool($object, $encode = true){
        return $encode ? ($object == 1 ? 1 : 0) : ($object == 1 ? true : false);
   }
}
