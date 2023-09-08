<?php

declare(strict_types=1);

namespace PostgresLearning;

use PDO;

final class IndexManager
{
    public static function create(PDO $connection, string $table, string $column): void
    {
        $connection->exec(<<<SQL
            CREATE INDEX {$column}_idx ON {$table} USING btree ({$column});
        SQL);

        echo "Создан btree индекс для таблицы: {$table} в {$column} \n";
    }

    public static function createComposite(PDO $connection, string $table, string ...$columns): void
    {
        $columnsTxt = implode(', ', $columns);
        $idxName = implode('_', $columns);

        $connection->exec(<<<SQL
            CREATE INDEX {$idxName}_composite_idx ON {$table} USING btree ({$columnsTxt});
        SQL);

        echo "Создан составной индекс btree для таблицы: {$table} в {$columnsTxt} \n";
    }

    public static function createGin(PDO $connection, string $table, string $column): void
    {
        $connection->exec(<<<SQL
            CREATE INDEX {$column}_idx ON {$table} USING gin ({$column} gin_trgm_ops);
        SQL);

        echo "Создан gin индекс для таблицы: {$table} в {$column} \n";
    }

    public static function drop(PDO $connection, string $column): void
    {
        $connection->exec(<<<SQL
            DROP INDEX {$column}_idx;
        SQL);

        echo "Удален индекс {$column}_idx \n";
    }
}
