<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../src/search.php';

include __DIR__ . '/../templates/header.php';
?>

<section class="text-center mb-4">
    <h2 class="mb-2">Encontre seu Digimon</h2>
    <p class="text-muted mb-0">Busque por nome, nível e tipo. Você pode combinar os filtros.</p>
</section>

<section class="row justify-content-center mb-4">
    <div class="col-lg-10">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="index.php" method="GET" class="row g-3">
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

                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Pesquisar</button>
                        <a href="index.php" class="btn btn-outline-secondary">Limpar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../templates/search_results.php'; ?>

<?php include __DIR__ . '/../templates/footer.php'; ?>