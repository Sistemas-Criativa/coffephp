<?php

namespace Core;

use Models\User;
use Core\Request;
use Core\Utilities;
use Models\Session;

class Auth extends Request
{
    /**
     * Authenticate the user
     */
    public final static function auth()
    {
        $validator = Validator::validate(Request::post(), [
            'Email' => ['required:O e-mail é obrigatório', 'email:E-mail inválido'],
            'Password' => ['required:A senha é obrigatória', 'min#8:A senha deve ter no mínimo 8', 'max#255:senha deve ter no máximo 16']
        ]);
        if ($validator->errors() !=  false) {
            return $validator->errors();
        }
        $user = User::select()
            ->where('Active', '=', '1')
            ->and('User', '=', Utilities::hash(Request::post()['Email']))
            ->and('Password', '=', Utilities::hash(Request::post()['Password']))
            ->limit(1)
            ->execute();
        if (count($user) > 0) {
            $session = Session::create(["id_User" => $user[0]['id'], "Token" => Utilities::token(), "Status" => 1]);
            Request::saveDataSession('user', $session);
            return true;
        } else {
            $validator->addError('Usuário ou senha incorretos.');
            return $validator->errors();
        }
    }

    /**
     * Sign up the user
     */
    public final static function sign()
    {
        $validator = Validator::validate(Request::post(), [
            'Email' => ['required:O e-mail é obrigatório', 'email:E-mail inválido'],
            'Password' => ['required:A senha é obrigatória', 'min#8:A senha deve ter no mínimo 8', 'max#255:A senha deve ter no máximo 16', 'confirmed:As senhas não são iguais']
        ]);
        if ($validator->errors() !=  false) {
            return $validator->errors();
        }
        $user = User::select()
            ->Where('User', '=', Utilities::hash(Request::post()['Email']))
            ->and('Password', '=', Utilities::hash(Request::post()['Password']))
            ->limit(1)
            ->execute();
        if (count($user) == 0) {
            $data = Request::post();
            $data['Active'] = 1;
            $data['User'] =  Utilities::hash($data['Email']);
            $data['Password'] =  Utilities::hash($data['Password']);
            $user = User::create($data);
            $session = Session::create(["id_User" => $user['id'], "Token" => Utilities::token(), "Status" => 1]);
            Request::saveDataSession('user', $session);
            return true;
        } else {
            $validator->addError('Usuário já cadastrado.');
            return $validator->errors();
        }
    }

    /** verify if is logged */
    public final static function user()
    {
        $session = Request::session('user');
        if ($session === false || !isset($session['Token'])) {
            return false;
        } else {
            $logged = User::selectCount("id", "LOGGED")
                ->where()
                ->in("id")
                ->select(['id_User'], Session::tableName())
                ->where('Token', "=", $session['Token'])
                ->and('Status', '=', '1')
                ->endin()
                ->execute();
            if (!$logged) {
                return false;
            } else {
                if ($logged[0]['LOGGED'] == 1) {
                    return $session;
                } else {
                    Request::clearDataSession('user');
                    return false;
                }
            }
        }
    }
    /** Loggout the user */
    public final static function logout()
    {
        $session = Request::session('user');
        if ($session == false) {
            return false;
        } else {
            Session::update(['Status' => '0'])
                ->where('Token', "=", $session['Token'])
                ->execute();
        }
        Request::clearDataSession('user');
    }
}
