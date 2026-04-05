<?php

declare(strict_types=1);

final class DigimonMapper
{
    public static function fromListItem(array $payload): array
    {
        return [
            'name' => (string) ($payload['name'] ?? 'Desconhecido'),
            'image' => (string) ($payload['image'] ?? ''),
            'href' => (string) ($payload['href'] ?? ''),
            'level' => self::firstValue($payload['levels'] ?? [], 'level'),
            'attribute' => self::firstValue($payload['attributes'] ?? [], 'attribute'),
        ];
    }

    public static function fromDetailsPayload(array $payload): array
    {
        return [
            'name' => (string) ($payload['name'] ?? 'Desconhecido'),
            'image' => (string) ($payload['images'][0]['href'] ?? ($payload['image'] ?? '')),
            'levels' => self::valuesFromItems($payload['levels'] ?? [], 'level'),
            'attributes' => self::valuesFromItems($payload['attributes'] ?? [], 'attribute'),
            'types' => self::valuesFromItems($payload['types'] ?? [], 'type'),
            'descriptions' => self::descriptionList($payload['descriptions'] ?? []),
        ];
    }

    public static function selectPreferredDescription(array $descriptions): array
    {
        $preferred = null;
        $isPortuguese = false;

        foreach ($descriptions as $description) {
            $lang = self::normalizeLanguage((string) ($description['language'] ?? ''));
            if ($lang === 'pt' || $lang === 'pt-br' || $lang === 'portuguese' || $lang === 'portugues') {
                $preferred = $description;
                $isPortuguese = true;
                break;
            }
        }

        if ($preferred === null) {
            foreach ($descriptions as $description) {
                $lang = self::normalizeLanguage((string) ($description['language'] ?? ''));
                if ($lang === 'en' || $lang === 'en-us' || $lang === 'english') {
                    $preferred = $description;
                    break;
                }
            }
        }

        if ($preferred === null && count($descriptions) > 0) {
            $preferred = $descriptions[0];
        }

        return [
            'description' => $preferred,
            'is_portuguese' => $isPortuguese,
        ];
    }

    private static function firstValue(array $items, string $key): string
    {
        if (!is_array($items) || !isset($items[0]) || !is_array($items[0])) {
            return '';
        }

        return trim((string) ($items[0][$key] ?? ''));
    }

    private static function valuesFromItems(array $items, string $key): array
    {
        if (!is_array($items)) {
            return [];
        }

        $values = [];

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $value = trim((string) ($item[$key] ?? ''));
            if ($value !== '') {
                $values[] = $value;
            }
        }

        return $values;
    }

    private static function descriptionList(array $items): array
    {
        if (!is_array($items)) {
            return [];
        }

        $descriptions = [];

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $text = trim((string) ($item['description'] ?? ''));
            if ($text === '') {
                continue;
            }

            $descriptions[] = [
                'text' => $text,
                'language' => trim((string) ($item['language'] ?? '')),
            ];
        }

        return $descriptions;
    }

    private static function normalizeLanguage(string $language): string
    {
        $normalized = strtolower(trim($language));
        return str_replace(['_', ' '], '-', $normalized);
    }
}
