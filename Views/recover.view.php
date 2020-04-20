<?php include('logo.view.php') ?>

<div class="row" id="login">
    <h3>Recuperação de senha</h3>
    <form method="POST" action="">
        <?php if (flashed('errors') != false) : ?>
            <?php foreach (flashed('errors') as $error) : ?>
                <div class="error"><?= $error ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if (flashed('message') != false) : ?>
            <div class="success"><?= flashed('message') ?></div>
        <?php endif; ?>
        <div>
            <label>E-mail</label>
            <input type="hidden" name="_token" value="<?= token() ?>" />
            <input type="email" required name="Email" placeholder="E-mail" />
        </div>
        <div class="row">
            <input type="submit" value="Recuperar" />
        </div>
    </form>
</div>

<?php include('footer.view.php') ?>

<style>
    #login {
        width: 50%;
        max-width: 400px;
        min-width: 200px;
        margin: auto;
        padding: 30px 20px;
        background: #f1f1f1;
        display: block;
        margin-top: 40px;
    }

    input {
        display: block;
        margin-bottom: 20px;
        width: 100%;
        border: solid 1px #dedede;
        padding: 10px;
    }

    input[type=submit] {
        background: #d4d4d4;
        margin-top: 10px;
        margin-bottom: 10px;
        width: 100%
    }

    input[type=button] {
        background: #e0e0e0;
        margin-top: 20px;
        margin-bottom: 0px;
        width: 100%
    }

    input[type=submit]:hover,
    input[type=button]:hover {
        background: #d0d0d0
    }

    h3 {
        margin-bottom: 20px;
        text-align: center
    }

    .error {
        color: #f00;
        margin-bottom: 5px;
        text-align: center
    }

    .success {
        color: #027902;
        margin-bottom: 5px;
        text-align: center
    }

    a:link {
        display: inline-block;
        padding: 0px;
        margin: 0px
    }

    .row {
        display: flex;
        flex-wrap: wrap;
    }

    .row>div {
        width: 50%;
        padding-right: 5px;
    }

    .row>div:last-child {
        padding-right: 0px
    }
</style>