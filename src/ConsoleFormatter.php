<?php

declare(strict_types=1);

namespace PostgresLearning;

final class ConsoleFormatter
{
    public static function openBlock(int $researchNo): void
    {
        echo "================================== Исследование №: {$researchNo} =========================================";
        echo PHP_EOL;
    }

    public static function closeBlock(): void
    {
        echo "==============================================================================================";
        echo PHP_EOL;
        echo PHP_EOL;
        echo PHP_EOL;
    }
}
