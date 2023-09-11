<?php

declare(strict_types=1);

namespace PostgresLearning;

use Fp\Collections\ArrayList;
use PDO;

final class SelectManager
{
    public static function select(PDO $connection, string $sql): void
    {
        $start = microtime(true);
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        $stmt->fetchAll();

        echo "Время выполнения `{$sql}`: " . '[' . microtime(true) - $start . ' сек.]' . PHP_EOL;
    }

    public static function explain(PDO $connection, string $sql): void
    {
        $stmt = $connection->prepare("EXPLAIN (COSTS OFF) {$sql};");
        $stmt->execute();

        $queryPlan = ArrayList::collect($stmt->fetchAll())
            ->map(fn(array $row) => $row['QUERY PLAN'])
            ->mkString(sep: PHP_EOL);

        echo "План выполнения запроса: \n{$queryPlan}\n";
    }
}
