<?php

declare(strict_types=1);

namespace PostgresLearning;

use Faker\Factory;
use Faker\Generator;
use Fp\Collections\ArrayList;
use Fp\Streams\Stream;
use PDO;

final class InsertManager
{
    private const CHUNK_SIZE = 1000;

    private const USERS_COUNT = 5000;
    private const POSTS_COUNT = 5000;
    private const COMMENTS_COUNT = 2000;
    private const LIKES_COUNT = 1000;
    private const TAGS_COUNT = 1000;

    public static function insert(PDO $connection): void
    {
        $faker = Factory::create('ru_RU');
        self::insertUsers($connection, $faker);
        self::insertPosts($connection, $faker);
        self::insertComments($connection, $faker);
        self::insertLikes($connection, $faker);
        self::insertTags($connection, $faker);
    }

    private static function insertUsers(PDO $connection, Generator $faker): void
    {
        $stmt = $connection->prepare(<<<SQL
            INSERT INTO users (id, name, age, email, created_at) VALUES (?, ?, ?, ?, ?);
        SQL);

        Stream::range(1, self::USERS_COUNT)
            ->map(fn(int $id) => [
                $id,
                $faker->name(),
                rand(18, 50),
                $faker->email(),
                $faker->dateTimeBetween('-1 year')->format('Y-m-d H:i:s'),
            ])
            ->chunks(self::CHUNK_SIZE)
            ->tap(function (ArrayList $rows) use ($stmt, $connection) {
                $connection->beginTransaction();
                $rows->tap(fn(array $row) => $stmt->execute($row));
                $connection->commit();
            })
            ->drain();
    }

    private static function insertPosts(PDO $connection, Generator $faker): void
    {
        $stmt = $connection->prepare(<<<SQL
            INSERT INTO posts (id, title, content, user_id, created_at) VALUES (?, ?, ?, ?, ?);
        SQL);

        Stream::range(1, self::POSTS_COUNT)
            ->map(fn(int $id) => [
                $id,
                $faker->title(),
                $faker->text(),
                rand(1, self::USERS_COUNT - 1),
                $faker->dateTimeBetween('-1 year')->format('Y-m-d H:i:s'),
            ])
            ->chunks(self::CHUNK_SIZE)
            ->tap(function (ArrayList $rows) use ($stmt, $connection) {
                $connection->beginTransaction();
                $rows->tap(fn(array $row) => $stmt->execute($row));
                $connection->commit();
            })
            ->drain();
    }

    private static function insertComments(PDO $connection, Generator $faker): void
    {
        $stmt = $connection->prepare(<<<SQL
            INSERT INTO comments (id, content, user_id, post_id, created_at) VALUES (?, ?, ?, ?, ?);
        SQL);

        Stream::range(1, self::COMMENTS_COUNT)
            ->map(fn(int $id) => [
                $id,
                $faker->text(),
                rand(1, self::USERS_COUNT - 1),
                rand(1, self::POSTS_COUNT - 1),
                $faker->dateTimeBetween('-1 year')->format('Y-m-d H:i:s'),
            ])
            ->chunks(self::CHUNK_SIZE)
            ->tap(function (ArrayList $rows) use ($stmt, $connection) {
                $connection->beginTransaction();
                $rows->tap(fn(array $row) => $stmt->execute($row));
                $connection->commit();
            })
            ->drain();
    }

    private static function insertLikes(PDO $connection, Generator $faker): void
    {
        $stmt = $connection->prepare(<<<SQL
            INSERT INTO likes (id, user_id, post_id, created_at) VALUES (?, ?, ?, ?);
        SQL);

        Stream::range(1, self::LIKES_COUNT)
            ->map(fn(int $id) => [
                $id,
                rand(1, self::USERS_COUNT - 1),
                rand(1, self::POSTS_COUNT - 1),
                $faker->dateTimeBetween('-1 year')->format('Y-m-d H:i:s'),
            ])
            ->chunks(self::CHUNK_SIZE)
            ->tap(function (ArrayList $rows) use ($stmt, $connection) {
                $connection->beginTransaction();
                $rows->tap(fn(array $row) => $stmt->execute($row));
                $connection->commit();
            })
            ->drain();
    }

    private static function insertTags(PDO $connection, Generator $faker): void
    {
        $stmt = $connection->prepare(<<<SQL
            INSERT INTO tags (id, name) VALUES (?, ?);
        SQL);

        Stream::range(1, self::TAGS_COUNT)
            ->map(fn(int $id) => [$id, $faker->word()])
            ->chunks(self::CHUNK_SIZE)
            ->tap(function (ArrayList $rows) use ($stmt, $connection) {
                $connection->beginTransaction();
                $rows->tap(fn(array $row) => $stmt->execute($row));
                $connection->commit();
            })
            ->drain();
    }
}