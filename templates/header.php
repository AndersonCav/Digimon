<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digimon Search</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&family=Rajdhani:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="header-shell py-3 mb-4">
    <div class="container d-flex flex-wrap align-items-center justify-content-between gap-2">
        <a href="index.php" class="text-decoration-none">
            <p class="brand-subtitle">Digital Monster Explorer</p>
            <h1 class="brand-title">Digimon Search</h1>
        </a>

        <nav class="d-flex flex-wrap gap-2">
            <a href="index.php" class="btn btn-outline-light btn-sm">Buscar</a>
            <?php if (\Auth::check()): ?>
                <a href="dashboard.php" class="btn btn-outline-light btn-sm">Dashboard</a>
                <a href="favoritos.php" class="btn btn-warning btn-sm">Favoritos</a>
                <a href="logout.php" class="btn btn-danger btn-sm">Sair</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary btn-sm">Entrar</a>
                <a href="register.php" class="btn btn-success btn-sm">Criar conta</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="container pb-4">
    <?php $flashError = flashGet('error'); ?>
    <?php if ($flashError !== null): ?>
        <div class="alert alert-danger" role="alert"><?php echo h($flashError); ?></div>
    <?php endif; ?>

    <?php $flashSuccess = flashGet('success'); ?>
    <?php if ($flashSuccess !== null): ?>
        <div class="alert alert-success" role="alert"><?php echo h($flashSuccess); ?></div>
    <?php endif; ?>