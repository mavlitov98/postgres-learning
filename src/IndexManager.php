<?php

declare(strict_types=1);

namespace PostgresLearning;

use PDO;

final class IndexManager
{
    public static function create(PDO $connection, string $table, string $column, string $idx): void
    {
        $connection->exec(<<<SQL
            CREATE INDEX {$idx} ON {$table} USING btree ({$column});
        SQL);

        echo "Создан btree индекс [{$idx}] для таблицы: {$table} в {$column} \n";
    }

    public static function createComposite(PDO $connection, string $table, string $idx, string ...$columns): void
    {
        $columnsTxt = implode(', ', $columns);

        $connection->exec(<<<SQL
            CREATE INDEX {$idx} ON {$table} USING btree ({$columnsTxt});
        SQL);

        echo "Создан составной индекс btree [{$idx}]для таблицы: {$table} в {$columnsTxt} \n";
    }

    public static function createGin(PDO $connection, string $table, string $column, string $idx): void
    {
        $connection->exec(<<<SQL
            CREATE INDEX {$idx} ON {$table} USING gin ({$column} gin_trgm_ops);
        SQL);

        echo "Создан gin индекс [{$idx}] для таблицы: {$table} в {$column} \n";
    }

    public static function drop(PDO $connection, string $idx): void
    {
        $connection->exec(<<<SQL
            DROP INDEX {$idx};
        SQL);

        echo "Удален индекс {$idx} \n";
    }
}
