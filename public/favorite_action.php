<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

\Auth::requireLogin('login.php');

if (!isPostRequest()) {
    redirect('index.php');
}

$returnUrl = inputString($_POST, 'return_url', 'index.php');

if ($returnUrl === '' || preg_match('/^https?:\/\//i', $returnUrl) === 1 || strpos($returnUrl, '//') === 0) {
    $returnUrl = 'index.php';
}

if (!csrfIsValid($_POST['_csrf'] ?? null)) {
    flashSet('error', 'A sessão expirou. Tente novamente.');
    redirect($returnUrl);
}

if ($conn === null) {
    flashSet('error', $dbError ?? 'Falha de conexão com o banco de dados.');
    redirect($returnUrl);
}

if (!($conn instanceof mysqli)) {
    flashSet('error', 'Conexão inválida com o banco de dados.');
    redirect($returnUrl);
}

$action = inputString($_POST, 'action');
$digimonName = inputString($_POST, 'digimon_name');

if ($digimonName === '') {
    flashSet('error', 'Nome do Digimon inválido.');
    redirect($returnUrl);
}

/** @var mysqli $conn */
$favoriteService = new \FavoriteService($conn);
$userId = (int) \Auth::id();

if ($action === 'add') {
    $added = $favoriteService->add($userId, [
        'name' => $digimonName,
        'image' => inputString($_POST, 'digimon_image'),
        'level' => inputString($_POST, 'digimon_level'),
        'attribute' => inputString($_POST, 'digimon_attribute'),
        'href' => inputString($_POST, 'digimon_href'),
    ]);

    flashSet($added ? 'success' : 'error', $added ? 'Digimon favoritado com sucesso.' : 'Não foi possível favoritar este Digimon.');
    redirect($returnUrl);
}

if ($action === 'remove') {
    $removed = $favoriteService->removeByName($userId, $digimonName);
    flashSet($removed ? 'success' : 'error', $removed ? 'Digimon removido dos favoritos.' : 'Não foi possível remover este favorito.');
    redirect($returnUrl);
}

flashSet('error', 'Ação inválida.');
redirect($returnUrl);
