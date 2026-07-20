<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($titulo ?? 'Blumiga') ?></title>
    <link rel="stylesheet" href="<?php echo asset('assets/css/style.css') ?>">
</head>
<body>

<nav class="navbar">
    <a href="<?php echo route('home') ?>" class="navbar-brand">
        ⚡ Blumiga
    </a>
    <button class="navbar-toggle" aria-label="Menu">
        <span></span>
        <span></span>
        <span></span>
    </button>
    <div class="navbar-links">
        <a href="<?php echo route('home') ?>">Início</a>
        <a href="<?php echo route('exemplo.banco') ?>">Banco</a>
        <a href="<?php echo route('exemplo.sessao') ?>">Sessão</a>
        <a href="<?php echo route('exemplo.helpers') ?>">Helpers</a>
        <a href="<?php echo route('admin.home') ?>">Admin</a>
        <?php if (session('logado')): ?>
            <a href="<?php echo route('logout') ?>">Sair</a>
        <?php else: ?>
            <a href="<?php echo route('login') ?>">Entrar</a>
        <?php endif; ?>
    </div>
</nav>

<main class="container">
    <?php echo $content ?? '' ?>
</main>

<footer class="footer">
    <p>Blumiga &mdash; Microframework MVC para PHP 8.4+</p>
    <p><a href="https://github.com/bluiceoficial/blumiga" target="_blank">GitHub</a></p>
</footer>

<script src="<?php echo asset('assets/js/main.js') ?>"></script>
</body>
</html>
