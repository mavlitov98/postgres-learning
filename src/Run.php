<?php

declare(strict_types=1);

use PostgresLearning\Connection;
use PostgresLearning\IndexManager;
use PostgresLearning\TablesManager;
use PostgresLearning\InsertDataManager;
use PostgresLearning\SelectDataManager;

require dirname(__DIR__) . '/vendor/autoload.php';

TablesManager::create(Connection::getConnection());
InsertDataManager::insert(Connection::getConnection());

SelectDataManager::select(Connection::getConnection(), $sql = "SELECT * FROM users WHERE name = 'Тест';");
IndexManager::create(Connection::getConnection(), 'users', 'name');
SelectDataManager::select(Connection::getConnection(), $sql);
echo PHP_EOL;

SelectDataManager::select(Connection::getConnection(), $sql = "SELECT * FROM users WHERE age = 21 AND email = 'test@gmail.com'");
IndexManager::createComposite(Connection::getConnection(), 'users', 'age', 'email');
SelectDataManager::select(Connection::getConnection(), $sql);
echo PHP_EOL;

SelectDataManager::select(Connection::getConnection(), $sql = "SELECT * FROM posts JOIN comments c on posts.id = c.post_id WHERE c.content = 'Тест'");
IndexManager::create(Connection::getConnection(), 'posts', 'content');
SelectDataManager::select(Connection::getConnection(), $sql);
echo PHP_EOL;

IndexManager::drop(Connection::getConnection(), 'name');
SelectDataManager::select(
    Connection::getConnection(),
    $sql = <<<SQL
        SELECT likes.post_id, u.name, p.title
            FROM likes
                     JOIN posts p ON p.id = likes.post_id
                     JOIN users u ON u.id = likes.user_id
                     JOIN comments c ON p.id = c.post_id
            WHERE u.name = 'Тест';
    SQL,
);
IndexManager::create(Connection::getConnection(), 'users', 'name');
SelectDataManager::select(Connection::getConnection(), $sql);
echo PHP_EOL;
echo PHP_EOL;

SelectDataManager::select(
    Connection::getConnection(),
    $sql = <<<SQL
        SELECT comments.content, p.title
        FROM comments
                 JOIN posts p on comments.post_id = p.id
        WHERE p.title LIKE '%Test%'
    SQL,
);
IndexManager::createGin(Connection::getConnection(), 'posts', 'title');
SelectDataManager::select(Connection::getConnection(), $sql);
echo PHP_EOL;
echo PHP_EOL;

TablesManager::drop(Connection::getConnection());