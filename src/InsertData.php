<?php

declare(strict_types=1);

namespace PostgresLearning;

use Faker\Factory;
use Faker\Generator;
use PDO;

final class InsertData
{
    private const USERS_COUNT = 2000;
    private const POSTS_COUNT = 2000;
    private const COMMENTS_COUNT = 2000;
    private const LIKES_COUNT = 2000;
    private const TAGS_COUNT = 2000;

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
        $rows = function () use ($faker) {
            foreach (range(1, self::USERS_COUNT) as $id) {
                yield [
                    $id,
                    $faker->name(),
                    rand(18, 50),
                    $faker->email(),
                    $faker->dateTimeBetween('-1 year')->format('Y-m-d H:i:s'),
                ];
            }
        };

        $stmt = $connection->prepare(<<<SQL
            INSERT INTO users (id, name, age, email, created_at) VALUES (?, ?, ?, ?, ?);
        SQL);

        foreach ($rows() as $row) {
            $stmt->execute($row);
        }
    }

    private static function insertPosts(PDO $connection, Generator $faker): void
    {
        $rows = function () use ($faker) {
            foreach (range(1, self::POSTS_COUNT) as $id) {
                yield [
                    $id,
                    $faker->title(),
                    $faker->text(),
                    rand(1, self::USERS_COUNT),
                    $faker->dateTimeBetween('-1 year')->format('Y-m-d H:i:s'),
                ];
            }
        };

        $stmt = $connection->prepare(<<<SQL
            INSERT INTO posts (id, title, content, user_id, created_at) VALUES (?, ?, ?, ?, ?);
        SQL);

        foreach ($rows() as $row) {
            $stmt->execute($row);
        }
    }

    private static function insertComments(PDO $connection, Generator $faker): void
    {
        $rows = function () use ($faker) {
            foreach (range(1, self::COMMENTS_COUNT) as $id) {
                yield [
                    $id,
                    $faker->text(),
                    rand(1, self::USERS_COUNT),
                    rand(1, self::POSTS_COUNT),
                    $faker->dateTimeBetween('-1 year')->format('Y-m-d H:i:s'),
                ];
            }
        };

        $stmt = $connection->prepare(<<<SQL
            INSERT INTO comments (id, content, user_id, post_id, created_at) VALUES (?, ?, ?, ?, ?);
        SQL);

        foreach ($rows() as $row) {
            $stmt->execute($row);
        }
    }

    private static function insertLikes(PDO $connection, Generator $faker): void
    {
        $rows = function () use ($faker) {
            foreach (range(1, self::LIKES_COUNT) as $id) {
                yield [
                    $id,
                    rand(1, self::USERS_COUNT),
                    rand(1, self::POSTS_COUNT),
                    $faker->dateTimeBetween('-1 year')->format('Y-m-d H:i:s'),
                ];
            }
        };

        $stmt = $connection->prepare(<<<SQL
            INSERT INTO likes (id, user_id, post_id, created_at) VALUES (?, ?, ?, ?);
        SQL);

        foreach ($rows() as $row) {
            $stmt->execute($row);
        }
    }

    private static function insertTags(PDO $connection, Generator $faker): void
    {
        $rows = function () use ($faker) {
            foreach (range(1, self::TAGS_COUNT) as $id) {
                yield [$id, $faker->word()];
            }
        };

        $stmt = $connection->prepare(<<<SQL
            INSERT INTO tags (id, name) VALUES (?, ?);
        SQL);

        foreach ($rows() as $row) {
            $stmt->execute($row);
        }
    }
}