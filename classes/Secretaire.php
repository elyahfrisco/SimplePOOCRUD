<?php
declare(strict_types=1);


require_once __DIR__ . '/../include/Database.php';

class Secretaire
{
    /** Connexion PDO partagée */
    private static function pdo(): PDO
    {
        return Database::connect();
    }

    /* -------- C R U D -------- */

    /** Ajout – renvoie l’ID créé */
    public static function insert(string $nom, string $prenom, string $adresse): int
    {
        $sql  = 'INSERT INTO secretaire (nom, prenom, adresse) VALUES (?,?,?)';
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute([$nom, $prenom, $adresse]);
        return (int) self::pdo()->lastInsertId();
    }

    /** Mise à jour */
    public static function update(int $id, string $nom, string $prenom, string $adresse): bool
    {
        $sql  = 'UPDATE secretaire SET nom=?, prenom=?, adresse=? WHERE Id_secretaire=?';
        $stmt = self::pdo()->prepare($sql);
        return $stmt->execute([$nom, $prenom, $adresse, $id]);
    }

    /** Suppression */
    public static function delete(int $id): bool
    {
        $sql = 'DELETE FROM secretaire WHERE Id_secretaire=?';
        return self::pdo()->prepare($sql)->execute([$id]);
    }

    /** Trouve un enregistrement ou null */
    public static function find(int $id): ?array
    {
        $sql  = 'SELECT Id_secretaire AS id, nom, prenom, adresse
                 FROM secretaire WHERE Id_secretaire=?';
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /** Recherche par nom ou prénom */
    public static function search(string $term): array
    {
        $sql  = 'SELECT Id_secretaire AS id, nom, prenom, adresse
                 FROM secretaire
                 WHERE nom LIKE :t OR prenom LIKE :t';
        $stmt = self::pdo()->prepare($sql);
        $like = '%'.$term.'%';
        $stmt->bindParam(':t', $like);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Liste complète */
    public static function all(): array
    {
        $sql = 'SELECT Id_secretaire AS id, nom, prenom, adresse
                FROM secretaire ORDER BY id DESC';
        return self::pdo()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}