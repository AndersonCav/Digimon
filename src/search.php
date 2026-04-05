<?php

declare(strict_types=1);

$itemsPerPage = 6;
$currentPage = max(1, (int) ($_GET['page'] ?? 1));

$filters = [
    'name' => inputString($_GET, 'txtDigimon'),
    'level' => inputString($_GET, 'txtNivel'),
    'attribute' => inputString($_GET, 'txtTipo'),
];

$availableLevels = [
    'Armor',
    'Adult',
    'Baby I',
    'Baby II',
    'Child',
    'Hybrid',
    'Perfect',
    'Ultimate',
    'Unknown',
];

$availableAttributes = [
    'Data',
    'Free',
    'No Data',
    'Unknown',
    'Vaccine',
    'Variable',
    'Virus',
];

$apiClient = new \DigimonApi($apiConfig);
$search = $apiClient->search($filters, $currentPage, $itemsPerPage);

$digimons = $search['items'];
$totalPages = (int) $search['total_pages'];
$searchError = $search['error'];

$favoriteNames = [];

if (\Auth::check() && $conn !== null) {
    $favoriteService = new \FavoriteService($conn);
    $favoriteNames = $favoriteService->getFavoriteNames((int) \Auth::id());
}

$favoriteMap = array_fill_keys($favoriteNames, true);

$queryParams = [
    'txtDigimon' => $filters['name'],
    'txtNivel' => $filters['level'],
    'txtTipo' => $filters['attribute'],
];