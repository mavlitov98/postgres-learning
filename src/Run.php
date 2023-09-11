<?php

declare(strict_types=1);

use PostgresLearning\ConsoleFormatter;
use PostgresLearning\Connection;
use PostgresLearning\IndexManager;
use PostgresLearning\TablesManager;
use PostgresLearning\InsertManager;
use PostgresLearning\SelectManager;

require dirname(__DIR__) . '/vendor/autoload.php';

$connection = Connection::get();
TablesManager::create($connection);
InsertManager::insert($connection);

ConsoleFormatter::openBlock(1);
SelectManager::select($connection, $sql = "SELECT * FROM users WHERE name = 'Тест';");
SelectManager::explain($connection, $sql);
IndexManager::create($connection, 'users', 'name', $idx = 'name_idx');
SelectManager::select($connection, $sql);
SelectManager::explain($connection, $sql);
IndexManager::drop($connection, $idx);
ConsoleFormatter::closeBlock();

ConsoleFormatter::openBlock(2);
SelectManager::select($connection, $sql = "SELECT * FROM users WHERE age = 21 AND email = 'test@gmail.com'");
SelectManager::explain($connection, $sql);
IndexManager::createComposite($connection, 'users', $idx = 'age_email_composite_idx', 'age', 'email');
SelectManager::select($connection, $sql);
SelectManager::explain($connection, $sql);
IndexManager::drop($connection, $idx);
ConsoleFormatter::closeBlock();

ConsoleFormatter::openBlock(3);
SelectManager::select($connection, $sql = "SELECT * FROM posts JOIN comments c on c.post_id = posts.id WHERE c.post_id = 2;");
SelectManager::explain($connection, $sql);
IndexManager::create($connection, 'comments', 'post_id', $idx = 'post_id_idx');
SelectManager::select($connection, $sql);
SelectManager::explain($connection, $sql);
IndexManager::drop($connection, $idx);
ConsoleFormatter::closeBlock();

ConsoleFormatter::openBlock(4);
SelectManager::select($connection, $sql = <<<SQL
    SELECT likes.post_id, u.name, p.title
    FROM likes
    JOIN posts p ON p.id = likes.post_id
    JOIN users u ON u.id = likes.user_id
    JOIN comments c ON p.id = c.post_id
    WHERE u.name = 'Тест';
SQL);
SelectManager::explain($connection, $sql);
IndexManager::create($connection, 'users', 'name', $idx = 'name_idx');
SelectManager::select($connection, $sql);
SelectManager::explain($connection, $sql);
IndexManager::drop($connection, $idx);
ConsoleFormatter::closeBlock();

ConsoleFormatter::openBlock(5);
SelectManager::select($connection, $sql = <<<SQL
    SELECT comments.content, p.title
    FROM comments
    JOIN posts p on comments.post_id = p.id
    WHERE p.title LIKE '%Test%'
SQL);
SelectManager::explain($connection, $sql);
IndexManager::createGin($connection, 'posts', 'title', $idx = 'posts_gin_idx');
SelectManager::select($connection, $sql);
SelectManager::explain($connection, $sql);
IndexManager::drop($connection, $idx);
ConsoleFormatter::closeBlock();

ConsoleFormatter::openBlock(6);
SelectManager::select($connection, $sql = <<<SQL
    SELECT name, age, EXTRACT(YEAR FROM age(current_date, created_at)) AS account_age, COUNT(*) AS email_count
    FROM users
    WHERE age >= 18
    GROUP BY name, age, created_at
    HAVING COUNT(*) > 1
    ORDER BY account_age DESC;
SQL);
SelectManager::explain($connection, $sql);
IndexManager::createComposite($connection, 'users', $idx = 'age_name_created_at_composite_idx', 'age', 'name', 'created_at');
SelectManager::select($connection, $sql);
SelectManager::explain($connection, $sql);
IndexManager::drop($connection, $idx);
ConsoleFormatter::closeBlock();

ConsoleFormatter::openBlock(7);
SelectManager::select($connection, $sql = <<<SQL
    SELECT u.name, p.title, c.content, COUNT(l.id) AS like_count
    FROM users u
    JOIN posts p ON p.user_id = u.id
    LEFT JOIN comments c ON c.post_id = p.id
    LEFT JOIN likes l ON l.post_id = p.id
    WHERE u.age >= 18
    GROUP BY u.name, p.title, c.content;
SQL);
SelectManager::explain($connection, $sql);
IndexManager::create($connection, 'posts', 'user_id', $idx = 'user_id_idx');
SelectManager::select($connection, $sql);
SelectManager::explain($connection, $sql);
IndexManager::drop($connection, $idx);
ConsoleFormatter::closeBlock();

TablesManager::drop($connection);