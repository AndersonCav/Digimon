<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

$reference = inputString($_GET, 'ref');
$error = null;
$digimon = null;

if ($reference === '') {
    $error = 'Referência de detalhe inválida.';
} else {
    $apiClient = new \DigimonApi($apiConfig);
    $details = $apiClient->getDetailsByReference($reference);
    $digimon = $details['item'];
    $error = $details['error'];
}

include __DIR__ . '/../templates/header.php';
?>

<section class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <p class="hero-eyebrow mb-1">Ficha técnica</p>
        <h2 class="h4 mb-0">Detalhes do Digimon</h2>
    </div>
    <a href="index.php" class="btn btn-outline-primary btn-sm">Voltar para busca</a>
</section>

<?php if ($error !== null): ?>
    <div class="alert alert-danger" role="alert"><?php echo h((string) $error); ?></div>
<?php elseif (!is_array($digimon)): ?>
    <div class="alert alert-warning" role="alert">Nenhum detalhe disponível para este Digimon.</div>
<?php else: ?>
    <?php
    $mapped = \DigimonMapper::fromDetailsPayload($digimon);
    $name = $mapped['name'];
    $image = $mapped['image'];
    $levels = $mapped['levels'];
    $attributes = $mapped['attributes'];
    $types = $mapped['types'];

    $descriptionSelection = \DigimonMapper::selectPreferredDescription($mapped['descriptions']);
    $selectedDescription = $descriptionSelection['description'];
    $isPortugueseDescription = $descriptionSelection['is_portuguese'];
    ?>

    <article class="card detail-card reveal-up">
        <div class="row g-0">
            <div class="col-lg-4">
                <?php if ($image !== ''): ?>
                    <img src="<?php echo h($image); ?>" alt="Imagem de <?php echo h($name); ?>" class="img-fluid rounded-start w-100 h-100 digimon-detail-image" style="max-height: 420px;">
                <?php else: ?>
                    <div class="h-100 d-flex align-items-center justify-content-center bg-light text-muted rounded-start" style="min-height: 280px;">
                        Imagem indisponível
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-8">
                <div class="card-body p-4">
                    <h3 class="h3 mb-3"><?php echo h($name); ?></h3>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="p-3 meta-grid-card">
                                <strong class="d-block mb-1">Níveis</strong>
                                <span class="text-muted"><?php echo h(count($levels) > 0 ? implode(', ', $levels) : 'Não informado'); ?></span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="p-3 meta-grid-card">
                                <strong class="d-block mb-1">Atributos</strong>
                                <span class="text-muted"><?php echo h(count($attributes) > 0 ? implode(', ', $attributes) : 'Não informado'); ?></span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="p-3 meta-grid-card">
                                <strong class="d-block mb-1">Tipos</strong>
                                <span class="text-muted"><?php echo h(count($types) > 0 ? implode(', ', $types) : 'Não informado'); ?></span>
                            </div>
                        </div>
                    </div>

                    <h4 class="h5 mb-2">Descrição</h4>
                    <?php if ($selectedDescription !== null): ?>
                        <p class="mb-2"><?php echo h((string) $selectedDescription['text']); ?></p>
                        <?php if ((string) $selectedDescription['language'] !== ''): ?>
                            <p class="small text-muted mb-0">Idioma: <?php echo h((string) $selectedDescription['language']); ?></p>
                        <?php endif; ?>

                        <?php if (!$isPortugueseDescription): ?>
                            <p class="small text-warning mb-0 mt-1">Descrição em português não disponível para este Digimon na API.</p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-muted mb-0">A API não retornou descrição para este Digimon.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </article>
<?php endif; ?>

<?php include __DIR__ . '/../templates/footer.php'; ?>
