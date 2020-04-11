<?php
namespace Core;
use Models\User;
use Core\Request;
use Core\Utilities;
use Models\Sessions;

class Auth extends Request{
    public final static function autenticar(){
        $user = User::select()
            ->where(['ativo','=','1'])
            ->and(['User','=',Utilities::hash('usuario')])
            ->and(['senha','=',Utilities::hash('senha')])
            ->limit(1)
            ->execute();
        if(count($user)>0){
            $session = Sessions::create(["idUser"=>"1", "Token" => Utilities::token(),"Status" => 1]);
            Request::saveSession($session);
            return true;
        } else {
            return false;
        }
    }

    /** Verifica if is logged */
    public final static function user(){
        $session = Request::session();
        if($session == false){
            return false;
        } else {
            $logged = User::selectCount("id", "LOGADOS")
            ->where()
            ->in("id")
                ->select(['idUser'], Sessions::tableName())
                ->where(['Token',"=", $session['Token']])
                ->and(['Status','=','1'])
            ->endin()
            ->execute();
            if(!$logged){
                return false;
            } else {
                if($logged[0]['LOGADOS']==1){
                    return $session;
                } else {
                    Request::clearSession();
                    return false;
                }
            }
        }
    }

}
?>