<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../src/search.php';

include __DIR__ . '/../templates/header.php';
?>

<section class="hero-panel mb-4 reveal-up">
    <p class="hero-eyebrow">Digivice Search Console</p>
    <h2 class="hero-title">Encontre seu Digimon favorito em segundos</h2>
    <p class="hero-subtitle">Combine nome, nível e tipo para criar buscas mais precisas e monte sua coleção personalizada.</p>
</section>

<section class="row justify-content-center mb-4">
    <div class="col-lg-10">
        <div class="card panel-card reveal-up">
            <div class="card-body p-4">
                <form id="search-form" action="index.php" method="GET" class="row g-3" data-loading-form data-loading-text="Pesquisando...">
                    <div class="col-md-4">
                        <label for="nomeDigimon" class="form-label">Nome</label>
                        <input
                            type="text"
                            class="form-control"
                            id="nomeDigimon"
                            name="txtDigimon"
                            placeholder="Ex.: Agumon"
                            value="<?php echo h($filters['name']); ?>"
                        >
                    </div>

                    <div class="col-md-4">
                        <label for="nivelDigimon" class="form-label">Nível</label>
                        <select class="form-select" id="nivelDigimon" name="txtNivel">
                            <option value="">Todos</option>
                            <?php foreach ($availableLevels as $levelOption): ?>
                                <option value="<?php echo h($levelOption); ?>" <?php echo $filters['level'] === $levelOption ? 'selected' : ''; ?>>
                                    <?php echo h($levelOption); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="tipoDigimon" class="form-label">Tipo</label>
                        <select class="form-select" id="tipoDigimon" name="txtTipo">
                            <option value="">Todos</option>
                            <?php foreach ($availableAttributes as $attributeOption): ?>
                                <option value="<?php echo h($attributeOption); ?>" <?php echo $filters['attribute'] === $attributeOption ? 'selected' : ''; ?>>
                                    <?php echo h($attributeOption); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 d-flex gap-2 flex-wrap">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-search me-1" aria-hidden="true"></i>
                            <span class="btn-label">Pesquisar</span>
                        </button>
                        <a href="index.php" class="btn btn-outline-secondary px-4">Limpar filtros</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<section id="search-loading-state" class="search-loading-state d-none" aria-hidden="true">
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php for ($i = 0; $i < 6; $i++): ?>
            <article class="col">
                <div class="card skeleton-card h-100">
                    <div class="skeleton skeleton-image"></div>
                    <div class="card-body">
                        <div class="skeleton skeleton-line w-75 mb-2"></div>
                        <div class="skeleton skeleton-line w-50 mb-3"></div>
                        <div class="d-flex gap-2">
                            <div class="skeleton skeleton-pill w-50"></div>
                            <div class="skeleton skeleton-pill w-25"></div>
                        </div>
                    </div>
                </div>
            </article>
        <?php endfor; ?>
    </div>
</section>

<div id="search-results-section">
    <?php include __DIR__ . '/../templates/search_results.php'; ?>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>