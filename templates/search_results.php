<?php

declare(strict_types=1);
?>

<section>
    <h3 class="h5 mb-3">
        <i class="bi bi-grid-3x3-gap-fill me-2" aria-hidden="true"></i>
        Resultados da busca
    </h3>

    <?php if ($searchError !== null): ?>
        <div class="alert alert-danger" role="alert"><?php echo h((string) $searchError); ?></div>
    <?php elseif (count($digimons) === 0): ?>
        <div class="alert alert-warning" role="alert">Nenhum Digimon encontrado para os filtros informados.</div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 results-grid">
            <?php foreach ($digimons as $digimon): ?>
                <?php
                $mapped = \DigimonMapper::fromListItem($digimon);
                $name = $mapped['name'];
                $image = $mapped['image'];
                $href = $mapped['href'];
                $level = $mapped['level'];
                $attribute = $mapped['attribute'];

                $isFavorite = isset($favoriteMap[$name]);
                 ?>
                <article class="col reveal-up">
                    <div class="card h-100 result-card">
                        <?php if ($image !== ''): ?>
                            <img src="<?php echo h($image); ?>" class="card-img-top" alt="Imagem de <?php echo h($name); ?>">
                        <?php endif; ?>

                        <div class="card-body d-flex flex-column">
                            <h4 class="h5 card-title mb-2"><?php echo h($name); ?></h4>

                            <div class="d-flex gap-2 mb-3 flex-wrap">
                                <?php if ($level !== ''): ?>
                                    <span class="meta-chip"><i class="bi bi-layers me-1" aria-hidden="true"></i>Nível: <?php echo h($level); ?></span>
                                <?php endif; ?>

                                <?php if ($attribute !== ''): ?>
                                    <span class="meta-chip"><i class="bi bi-shield-check me-1" aria-hidden="true"></i>Tipo: <?php echo h($attribute); ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex gap-2 mt-auto">
                                <?php if ($href !== ''): ?>
                                    <a
                                        href="digimon.php?ref=<?php echo h(urlencode($href)); ?>"
                                        class="btn btn-outline-primary btn-sm"
                                    >
                                        <i class="bi bi-info-circle me-1" aria-hidden="true"></i>
                                        Ver detalhes
                                    </a>
                                <?php endif; ?>

                                <?php if (\Auth::check()): ?>
                                    <form method="POST" action="favorite_action.php" class="ms-auto favorite-action-form" data-favorite-action-form>
                                        <?php echo csrfField(); ?>
                                        <input type="hidden" name="action" value="<?php echo $isFavorite ? 'remove' : 'add'; ?>">
                                        <input type="hidden" name="digimon_name" value="<?php echo h($name); ?>">
                                        <input type="hidden" name="digimon_image" value="<?php echo h($image); ?>">
                                        <input type="hidden" name="digimon_level" value="<?php echo h($level); ?>">
                                        <input type="hidden" name="digimon_attribute" value="<?php echo h($attribute); ?>">
                                        <input type="hidden" name="digimon_href" value="<?php echo h($href); ?>">
                                        <input type="hidden" name="return_url" value="index.php?<?php echo h(http_build_query(array_merge($queryParams, ['page' => $currentPage]))); ?>">
                                        <button type="submit" class="btn <?php echo $isFavorite ? 'btn-outline-danger' : 'btn-warning'; ?> btn-sm">
                                            <i class="bi <?php echo $isFavorite ? 'bi-heartbreak' : 'bi-heart-fill'; ?> me-1" aria-hidden="true"></i>
                                            <span class="btn-label"><?php echo $isFavorite ? 'Remover favorito' : 'Favoritar'; ?></span>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <nav aria-label="Paginação" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php if ($currentPage > 1): ?>
                        <?php $previousQuery = http_build_query(array_merge($queryParams, ['page' => $currentPage - 1])); ?>
                        <li class="page-item">
                            <a class="page-link" href="index.php?<?php echo h($previousQuery); ?>">Anterior</a>
                        </li>
                    <?php endif; ?>

                    <?php
                    $start = max(1, $currentPage - 2);
                    $end = min($totalPages, $currentPage + 2);
                    ?>

                    <?php for ($i = $start; $i <= $end; $i++): ?>
                        <?php $pageQuery = http_build_query(array_merge($queryParams, ['page' => $i])); ?>
                        <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                            <a class="page-link" href="index.php?<?php echo h($pageQuery); ?>"><?php echo h((string) $i); ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <?php $nextQuery = http_build_query(array_merge($queryParams, ['page' => $currentPage + 1])); ?>
                        <li class="page-item">
                            <a class="page-link" href="index.php?<?php echo h($nextQuery); ?>">Próxima</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</section>
