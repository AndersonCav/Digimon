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
    <h2 class="h4 mb-0">Detalhes do Digimon</h2>
    <a href="index.php" class="btn btn-outline-primary btn-sm">Voltar para busca</a>
</section>

<?php if ($error !== null): ?>
    <div class="alert alert-danger" role="alert"><?php echo h((string) $error); ?></div>
<?php elseif (!is_array($digimon)): ?>
    <div class="alert alert-warning" role="alert">Nenhum detalhe disponível para este Digimon.</div>
<?php else: ?>
    <?php
    $name = (string) ($digimon['name'] ?? 'Desconhecido');
    $image = (string) ($digimon['images'][0]['href'] ?? ($digimon['image'] ?? ''));

    $levels = [];
    if (isset($digimon['levels']) && is_array($digimon['levels'])) {
        foreach ($digimon['levels'] as $levelItem) {
            $label = trim((string) ($levelItem['level'] ?? ''));
            if ($label !== '') {
                $levels[] = $label;
            }
        }
    }

    $attributes = [];
    if (isset($digimon['attributes']) && is_array($digimon['attributes'])) {
        foreach ($digimon['attributes'] as $attributeItem) {
            $label = trim((string) ($attributeItem['attribute'] ?? ''));
            if ($label !== '') {
                $attributes[] = $label;
            }
        }
    }

    $types = [];
    if (isset($digimon['types']) && is_array($digimon['types'])) {
        foreach ($digimon['types'] as $typeItem) {
            $label = trim((string) ($typeItem['type'] ?? ''));
            if ($label !== '') {
                $types[] = $label;
            }
        }
    }

    $descriptions = [];
    if (isset($digimon['descriptions']) && is_array($digimon['descriptions'])) {
        foreach ($digimon['descriptions'] as $descriptionItem) {
            $text = trim((string) ($descriptionItem['description'] ?? ''));
            $language = trim((string) ($descriptionItem['language'] ?? ''));
            if ($text !== '') {
                $descriptions[] = [
                    'text' => $text,
                    'language' => $language,
                ];
            }
        }
    }

    $selectedDescription = null;
    $isPortugueseDescription = false;

    if (count($descriptions) > 0) {
        $normalizeLanguage = static function (string $language): string {
            $normalized = strtolower(trim($language));
            $normalized = str_replace(['_', ' '], '-', $normalized);

            return $normalized;
        };

        foreach ($descriptions as $description) {
            $language = $normalizeLanguage((string) $description['language']);

            if ($language === 'pt' || $language === 'pt-br' || $language === 'portuguese' || $language === 'portugues') {
                $selectedDescription = $description;
                $isPortugueseDescription = true;
                break;
            }
        }

        if ($selectedDescription === null) {
            foreach ($descriptions as $description) {
                $language = $normalizeLanguage((string) $description['language']);

                if ($language === 'en' || $language === 'en-us' || $language === 'english') {
                    $selectedDescription = $description;
                    break;
                }
            }
        }

        if ($selectedDescription === null) {
            $selectedDescription = $descriptions[0];
        }
    }
    ?>

    <article class="card border-0 shadow-sm">
        <div class="row g-0">
            <div class="col-lg-4">
                <?php if ($image !== ''): ?>
                    <img src="<?php echo h($image); ?>" alt="Imagem de <?php echo h($name); ?>" class="img-fluid rounded-start w-100 h-100" style="object-fit: cover; max-height: 420px;">
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
                            <div class="p-3 bg-light rounded">
                                <strong class="d-block mb-1">Níveis</strong>
                                <span class="text-muted"><?php echo h(count($levels) > 0 ? implode(', ', $levels) : 'Não informado'); ?></span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <strong class="d-block mb-1">Atributos</strong>
                                <span class="text-muted"><?php echo h(count($attributes) > 0 ? implode(', ', $attributes) : 'Não informado'); ?></span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
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
