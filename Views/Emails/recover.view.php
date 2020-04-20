<html>
<meta charset="utf-8" />

<body style="padding: 20px">
    <img src="<?= assets() ?>/images/logo.png" />
    <h1>Recuperação de conta</h1>
    <p>
        Caro <b><?= $user['Name'] ?></b>.
    </p>
    <p>
        Você solicitou a redefinição de senha em <b><?= date('d/m/Y') ?></b>, para continuar basta acessar o link abaixo.
    </p>
    <p>
        <a href="<?= route('show.reset', true, ['token' => $token]) ?>" style="color: #fff; background: #000; display: block; padding: 10px 30px; text-decoration: none !important">RECUPERAR MINHA SENHA</a>
    </p>
    <hr>
    <p style="font-size: 0.8rem">
        Se voce não solicitou a redefinição de senha, não há necessidade de tomar nenhuma ação.
    </p>
</body>

</html>