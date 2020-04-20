<?php

namespace Controllers;

use Core\Controllers;
use Core\Auth;
use Core\Validator;
use Core\Request;
use Models\User;
use Core\Utilities;
use Core\Email;
use Models\Recover;

class RecoverController extends Controllers
{
    /**
     * Show view for recover form
     */
    public function show()
    {
        if (Auth::user()) {
            redirect()->route('dashboard');
        }
        view("master.view", ["title" => "Login - CoffePHP", 'include' => 'recover.view']);
    }

    public function showReset($token)
    {
        if (Auth::user()) {
            redirect()->route('dashboard');
        }
        $valid = Recover::selectCount('id_User', 'VALID')->where('Token', '=', $token)->and('Status', '=', '0')->and('TIMESTAMPDIFF(HOUR,NOW(),Date)', '<', '1')->execute();
        if ($valid[0]['VALID'] == 1) {
            view("master.view", ["title" => "Recuperção de senha - CoffePHP", 'include' => 'reset.view', 'token' => $token]);
        } else {
            redirect()->route('show.recover', ['errors' => ['O código não é valido, inicie novamente o processo de redefinição']]);
        }
    }

    public function reset($token)
    {
        $validator = Validator::validate(Request::post(), [
            'Password' => ['required:O e-mail é obrigatório', 'min#8:A quantidade mínima de caracteres é 8.', 'max#16:A quantidade máxim de caracteres é 16.', 'confirmed:As senhas não são iguais'],
        ]);
        if ($validator->errors() !=  false) {
            redirect()->back(['errors' => $validator->errors()]);
        }
        $user = User::select()
            ->where()
            ->in('id')
            ->select(['id_User'], Recover::tableName())
            ->where('Token', '=', $token)
            ->and('Status', '=', '0')
            ->endin()
            ->limit(1)
            ->execute();
        if (count($user) > 0) {
            User::update(['Password' => Utilities::hash(Request::post()['Password'])])->where('id', '=', $user[0]['id'])->execute();
            Recover::update(['Status', '=',  '1'])->where('Token', '=', $token)->execute();
            redirect()->route('show.login', ['message' => 'Senha alterada com sucesso, agora você já pode fazer login']);
        } else {
            redirect()->route('show.recover', ['errors' => ['O código não é valido, inicie novamente o processo de redefinição']]);
        }
    }

    /**
     * Initiates the recovery
     */
    public function recover()
    {
        $validator = Validator::validate(Request::post(), [
            'Email' => ['required:O e-mail é obrigatório', 'email:E-mail inválido'],
        ]);
        if ($validator->errors() !=  false) {
            redirect()->back(['errors' => $validator->errors()]);
        }
        $user = User::select()
            ->where('User', '=', Utilities::hash(Request::post()['Email']))
            ->limit(1)
            ->execute();
        if (count($user) > 0) {
            $token =  Utilities::token();
            Recover::create(['id_User' => $user[0]['id'], 'Token' => $token]);
            Email::to(Request::post()['Email'])
                ->subject('Redefinição de senha')
                ->message(view('Emails\recover.view', ['user' => $user[0], 'token' => $token]))
                ->send();
            redirect()->back(['message' => 'Um e-mail de redefinição foi enviado, por favor, verifique-o para redefinir sua senha']);
        } else {
            $validator->addError('Usuário não encontrado.');
            redirect()->back(['errors' => $validator->errors()]);
        }
    }
}
