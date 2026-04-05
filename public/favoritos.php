<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

\Auth::requireLogin('login.php');

$favorites = [];
$error = null;

if ($conn === null) {
    $error = $dbError ?? 'Falha de conexão com o banco de dados.';
} else {
    $favoriteService = new \FavoriteService($conn);
    $favorites = $favoriteService->listByUser((int) \Auth::id());
}

include __DIR__ . '/../templates/header.php';
?>

<section class="mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <p class="hero-eyebrow mb-1">Coleção pessoal</p>
            <h2 class="h4 mb-0">Meus favoritos</h2>
        </div>
        <a href="index.php" class="btn btn-outline-primary btn-sm">Voltar para busca</a>
    </div>
</section>

<?php if ($error !== null): ?>
    <div class="alert alert-danger" role="alert"><?php echo h($error); ?></div>
<?php elseif (count($favorites) === 0): ?>
    <div class="alert alert-info" role="alert">Você ainda não possui Digimons favoritados.</div>
<?php else: ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($favorites as $favorite): ?>
            <article class="col reveal-up">
                <div class="card h-100 favorite-card">
                    <?php if (!empty($favorite['digimon_image'])): ?>
                        <img
                            src="<?php echo h((string) $favorite['digimon_image']); ?>"
                            class="card-img-top"
                            alt="Imagem de <?php echo h((string) $favorite['digimon_name']); ?>"
                        >
                    <?php endif; ?>

                    <div class="card-body d-flex flex-column">
                        <h3 class="h5 card-title"><?php echo h((string) $favorite['digimon_name']); ?></h3>

                        <div class="d-flex gap-2 mb-2 flex-wrap">
                            <?php if (!empty($favorite['digimon_level'])): ?>
                                <span class="meta-chip">Nível: <?php echo h((string) $favorite['digimon_level']); ?></span>
                            <?php endif; ?>

                            <?php if (!empty($favorite['digimon_attribute'])): ?>
                                <span class="meta-chip">Tipo: <?php echo h((string) $favorite['digimon_attribute']); ?></span>
                            <?php endif; ?>
                        </div>

                        <p class="small text-secondary mt-auto mb-3">
                            Favoritado em <?php echo h((string) $favorite['data_adicionado']); ?>
                        </p>

                        <div class="d-flex gap-2">
                            <?php if (!empty($favorite['digimon_href'])): ?>
                                <a
                                    href="digimon.php?ref=<?php echo h(urlencode((string) $favorite['digimon_href'])); ?>"
                                    class="btn btn-outline-primary btn-sm"
                                >
                                    Ver detalhes
                                </a>
                            <?php endif; ?>

                            <form method="POST" action="favorite_action.php" class="ms-auto">
                                <?php echo csrfField(); ?>
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="digimon_name" value="<?php echo h((string) $favorite['digimon_name']); ?>">
                                <input type="hidden" name="return_url" value="favoritos.php">
                                <button type="submit" class="btn btn-outline-danger btn-sm">Remover</button>
                            </form>
                        </div>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../templates/footer.php'; ?>