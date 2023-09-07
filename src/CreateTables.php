<?php

declare(strict_types=1);

namespace PostgresLearning;

use PDO;

final class CreateTables
{
    public static function create(PDO $connection): void
    {
        $connection->exec(<<<SQL
            CREATE TABLE IF NOT EXISTS users (
                id SERIAL PRIMARY KEY,
                name VARCHAR(50),
                age INT,
                email VARCHAR(50),
                created_at TIMESTAMPTZ DEFAULT NOW()
            );
        SQL);

        $connection->exec(<<<SQL
            CREATE TABLE IF NOT EXISTS posts (
                id SERIAL PRIMARY KEY,
                title VARCHAR(100),
                content TEXT,
                user_id INT REFERENCES users(id),
                created_at TIMESTAMPTZ DEFAULT NOW()
            );
        SQL);

        $connection->exec(<<<SQL
            CREATE TABLE IF NOT EXISTS comments (
                id SERIAL PRIMARY KEY,
                content TEXT,
                user_id INT REFERENCES users(id),
                post_id INT REFERENCES posts(id),
                created_at TIMESTAMPTZ DEFAULT NOW()
            );
        SQL);

        $connection->exec(<<<SQL
            CREATE TABLE IF NOT EXISTS likes (
                id SERIAL PRIMARY KEY,
                user_id INT REFERENCES users(id),
                post_id INT REFERENCES posts(id),
                created_at TIMESTAMPTZ DEFAULT NOW()
            );
        SQL);

        $connection->exec(<<<SQL
            CREATE TABLE IF NOT EXISTS tags (
                id SERIAL PRIMARY KEY,
                name VARCHAR(50)
            );
        SQL);
    }
}