<?php include('logo.view.php') ?>
<div class="row">
    <h1 class="col row">Bem vindo</h1>
</div>
<div class="row">
    <a href="<?php echo route('home'); ?>">Home</a>
    <?php if ($user == false) : ?>
        <a href="<?php echo route('show.login', true); ?>">Login</a>
        <a href="<?php echo route('show.signup'); ?>">Cadastro</a>
    <?php endif ?>
    <a href="<?php echo route('dashboard'); ?>">Área protegida</a>
    <?php if ($user != false) : ?>
        <a href="<?php echo route('do.logout'); ?>">Sair</a>
    <?php endif ?>
</div>