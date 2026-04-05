<?php

declare(strict_types=1);

final class DigimonApi
{
    private string $baseUrl;
    private bool $cacheEnabled;
    private string $cacheDir;
    private int $cacheTtl;

    public function __construct(array $apiConfig)
    {
        $this->baseUrl = rtrim((string) ($apiConfig['base_url'] ?? ''), '/');
        $this->cacheEnabled = (bool) ($apiConfig['cache_enabled'] ?? true);
        $this->cacheDir = (string) ($apiConfig['cache_dir'] ?? BASE_PATH . '/storage/cache');
        $this->cacheTtl = (int) ($apiConfig['cache_ttl_seconds'] ?? 300);

        if ($this->cacheEnabled && !is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0775, true);
        }
    }

    public function search(array $filters, int $page, int $pageSize): array
    {
        $name = trim((string) ($filters['name'] ?? ''));
        $level = trim((string) ($filters['level'] ?? ''));
        $attribute = trim((string) ($filters['attribute'] ?? ''));

        $params = [
            'page' => max(0, $page - 1),
            'pageSize' => max(1, $pageSize),
        ];

        if ($name !== '') {
            $params['name'] = $name;
        }

        if ($level !== '') {
            $params['level'] = $level;
        }

        if ($attribute !== '') {
            $params['attribute'] = $attribute;
        }

        $response = $this->request($params);

        if ($response['error'] !== null) {
            return [
                'items' => [],
                'total_pages' => 1,
                'error' => 'Não foi possível consultar a API de Digimon agora. Tente novamente em instantes.',
            ];
        }

        $payload = $response['data'];

        if (!is_array($payload)) {
            return [
                'items' => [],
                'total_pages' => 1,
                'error' => 'A API retornou um formato inesperado.',
            ];
        }

        $items = isset($payload['content']) && is_array($payload['content']) ? $payload['content'] : [];

        if ($name !== '') {
            $items = array_values(array_filter(
                $items,
                static fn (array $digimon): bool => stripos((string) ($digimon['name'] ?? ''), $name) !== false
            ));
        }

        if ($level !== '') {
            $items = array_values(array_filter(
                $items,
                static fn (array $digimon): bool => self::hasLevel($digimon, $level)
            ));
        }

        if ($attribute !== '') {
            $items = array_values(array_filter(
                $items,
                static fn (array $digimon): bool => self::hasAttribute($digimon, $attribute)
            ));
        }

        $totalPages = 1;
        if (isset($payload['pageable']['totalPages']) && is_numeric($payload['pageable']['totalPages'])) {
            $totalPages = max(1, (int) $payload['pageable']['totalPages']);
        }

        if ($name !== '') {
            $totalPages = 1;
        }

        return [
            'items' => $items,
            'total_pages' => $totalPages,
            'error' => null,
        ];
    }

    public function getDetailsByReference(string $referenceUrl): array
    {
        $referenceUrl = trim($referenceUrl);

        if ($referenceUrl === '' || !$this->isAllowedReferenceUrl($referenceUrl)) {
            return [
                'item' => null,
                'error' => 'Referência de Digimon inválida.',
            ];
        }

        $response = $this->requestUrl($referenceUrl);

        if ($response['error'] !== null || !is_array($response['data'])) {
            return [
                'item' => null,
                'error' => 'Não foi possível carregar os detalhes deste Digimon.',
            ];
        }

        return [
            'item' => $response['data'],
            'error' => null,
        ];
    }

    private function request(array $params): array
    {
        if ($this->baseUrl === '') {
            return ['data' => null, 'error' => 'missing_base_url'];
        }

        $query = http_build_query($params);
        $url = $this->baseUrl . '?' . $query;

        return $this->requestUrl($url);
    }

    private function requestUrl(string $url): array
    {
        $url = trim($url);

        if ($url === '') {
            return ['data' => null, 'error' => 'missing_url'];
        }

        if ($this->cacheEnabled) {
            $cached = $this->readCache($url);
            if ($cached !== null) {
                return ['data' => $cached, 'error' => null];
            }
        }

        $raw = null;
        $httpCode = 0;
        $error = '';

        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_HTTPHEADER => ['Accept: application/json'],
            ]);

            $raw = curl_exec($ch);
            $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
        } else {
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'timeout' => 10,
                    'header' => "Accept: application/json\r\n",
                ],
            ]);

            $raw = file_get_contents($url, false, $context);
            $httpCode = is_array($http_response_header ?? null)
                ? $this->extractHttpCode($http_response_header)
                : 200;
        }

        if ($raw === false || $httpCode >= 400 || $error !== '') {
            return ['data' => null, 'error' => 'request_failed'];
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return ['data' => null, 'error' => 'invalid_json'];
        }

        if ($this->cacheEnabled) {
            $this->writeCache($url, $decoded);
            $this->cleanupCache();
        }

        return ['data' => $decoded, 'error' => null];
    }

    private function isAllowedReferenceUrl(string $url): bool
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $baseHost = (string) parse_url($this->baseUrl, PHP_URL_HOST);
        $targetHost = (string) parse_url($url, PHP_URL_HOST);

        if ($baseHost === '' || $targetHost === '') {
            return false;
        }

        return strcasecmp($baseHost, $targetHost) === 0;
    }

    private function cacheFilePath(string $url): string
    {
        return $this->cacheDir . '/api_' . md5($url) . '.json';
    }

    private function readCache(string $url): ?array
    {
        $filePath = $this->cacheFilePath($url);

        if (!is_file($filePath)) {
            return null;
        }

        if ((time() - filemtime($filePath)) > $this->cacheTtl) {
            return null;
        }

        $raw = file_get_contents($filePath);
        if ($raw === false) {
            return null;
        }

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : null;
    }

    private function writeCache(string $url, array $payload): void
    {
        $filePath = $this->cacheFilePath($url);
        file_put_contents($filePath, json_encode($payload, JSON_PRETTY_PRINT));
    }

    private function cleanupCache(): void
    {
        $files = glob($this->cacheDir . '/api_*.json');
        if (!is_array($files)) {
            return;
        }

        $maxAge = $this->cacheTtl;
        foreach ($files as $file) {
            if (!is_file($file)) {
                continue;
            }

            if ((time() - filemtime($file)) > $maxAge) {
                @unlink($file);
            }
        }
    }

    private static function hasLevel(array $digimon, string $level): bool
    {
        if (!isset($digimon['levels']) || !is_array($digimon['levels'])) {
            return false;
        }

        foreach ($digimon['levels'] as $item) {
            if (strcasecmp((string) ($item['level'] ?? ''), $level) === 0) {
                return true;
            }
        }

        return false;
    }

    private static function hasAttribute(array $digimon, string $attribute): bool
    {
        if (!isset($digimon['attributes']) || !is_array($digimon['attributes'])) {
            return false;
        }

        foreach ($digimon['attributes'] as $item) {
            if (strcasecmp((string) ($item['attribute'] ?? ''), $attribute) === 0) {
                return true;
            }
        }

        return false;
    }

    private function extractHttpCode(array $responseHeaders): int
    {
        foreach ($responseHeaders as $header) {
            if (preg_match('/^HTTP\/\S+\s+(\d{3})/', $header, $matches) === 1) {
                return (int) $matches[1];
            }
        }

        return 200;
    }
}
