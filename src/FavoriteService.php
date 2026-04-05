<?php

declare(strict_types=1);

final class FavoriteService
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function listByUser(int $userId): array
    {
        $sql = 'SELECT id, digimon_name, digimon_image, digimon_level, digimon_attribute, digimon_href, data_adicionado FROM favoritos WHERE user_id = ? ORDER BY data_adicionado DESC';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function isFavorite(int $userId, string $digimonName): bool
    {
        $sql = 'SELECT 1 FROM favoritos WHERE user_id = ? AND digimon_name = ? LIMIT 1';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('is', $userId, $digimonName);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result !== false && $result->num_rows > 0;
    }

    public function getFavoriteNames(int $userId): array
    {
        $sql = 'SELECT digimon_name FROM favoritos WHERE user_id = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            return [];
        }

        $names = [];
        while ($row = $result->fetch_assoc()) {
            $names[] = (string) ($row['digimon_name'] ?? '');
        }

        return $names;
    }

    public function add(int $userId, array $digimonData): bool
    {
        $name = trim((string) ($digimonData['name'] ?? ''));

        if ($name === '') {
            return false;
        }

        if ($this->isFavorite($userId, $name)) {
            return true;
        }

        $image = trim((string) ($digimonData['image'] ?? ''));
        $level = trim((string) ($digimonData['level'] ?? ''));
        $attribute = trim((string) ($digimonData['attribute'] ?? ''));
        $href = trim((string) ($digimonData['href'] ?? ''));

        $sql = 'INSERT INTO favoritos (user_id, digimon_name, digimon_image, digimon_level, digimon_attribute, digimon_href) VALUES (?, ?, ?, ?, ?, ?)';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('isssss', $userId, $name, $image, $level, $attribute, $href);

        return $stmt->execute();
    }

    public function removeByName(int $userId, string $digimonName): bool
    {
        $name = trim($digimonName);

        if ($name === '') {
            return false;
        }

        $sql = 'DELETE FROM favoritos WHERE user_id = ? AND digimon_name = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('is', $userId, $name);

        return $stmt->execute();
    }
}
