<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

\Auth::requireLogin('login.php');

include __DIR__ . '/../templates/header.php';
?>

<section class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h2 class="h4">Olá, <?php echo h((string) \Auth::username()); ?>!</h2>
                <p class="text-muted">Sua sessão está ativa. A partir daqui você pode gerenciar seus favoritos e continuar suas buscas.</p>

                <div class="d-flex gap-2 flex-wrap">
                    <a href="index.php" class="btn btn-primary">Ir para busca</a>
                    <a href="favoritos.php" class="btn btn-warning">Meus favoritos</a>
                    <a href="logout.php" class="btn btn-outline-danger">Sair</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>