<?php

declare(strict_types=1);

namespace PostgresLearning;

use PDO;

final class SelectDataManager
{
    public static function select(PDO $connection, string $sql): void
    {
        $start = microtime(true);
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        $stmt->fetchAll();

        echo "Время выполнения `{$sql}`: " . '[' . microtime(true) - $start . ' секунды.]' . PHP_EOL;
    }
}
